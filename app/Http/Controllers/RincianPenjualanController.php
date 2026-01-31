<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\Stock;
use App\Models\Gudang;
use App\Models\GudangStock;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\RincianPenjualan;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class RincianPenjualanController extends Controller
{
    public function index($penjualan_id)
    {
        $penjualan = Penjualan::findOrFail($penjualan_id);
        $rincianpenjualans = RincianPenjualan::where('penjualan_id', $penjualan_id)->get();
        return view('rincianpenjualan.index', compact('penjualan', 'rincianpenjualans'));
    }

    public function create($penjualan_id)
    {
        $penjualan = Penjualan::findOrFail($penjualan_id);
        $stocks = Stock::all();
        $gudangs = Gudang::all();
        return view('rincianpenjualan.create', compact('penjualan', 'stocks', 'gudangs'));
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'penjualan_id' => 'required|exists:penjualan,id',
            'items' => 'required|array',
            'items.*.name' => 'required|string|exists:stocks,name',
            'items.*.quantity' => 'required|integer|min:1',
            'gudangs_id' => 'required|exists:gudangs,id',
        ]);

        $penjualanId = $validated['penjualan_id'];
        $items = $validated['items'];
        $gudangId = $validated['gudangs_id'];

        $totalAmount = 0;
        $insufficientStockItems = [];

        DB::transaction(function () use ($items, $penjualanId, $gudangId, &$totalAmount, &$insufficientStockItems) {
            foreach ($items as $item) {
                $stock = Stock::where('name', $item['name'])->first();
                $gudangStock = GudangStock::where('stocks_id', $stock->id)
                                          ->where('gudangs_id', $gudangId)
                                          ->lockForUpdate()
                                          ->first();

                // Check if the warehouse stock quantity is sufficient
                if (!$gudangStock || $gudangStock->quantity < $item['quantity']) {
                    $insufficientStockItems[] = [
                        'stock_name' => $stock->name,
                        'required_quantity' => $item['quantity'],
                        'available_quantity' => $gudangStock ? $gudangStock->quantity : 0
                    ];
                    continue;
                }

                $price = $stock->jual;

                // Create RincianPenjualan record
                RincianPenjualan::create([
                    'penjualan_id' => $penjualanId,
                    'stocks_id' => $stock->id,
                    'gudangs_id' => $gudangId,
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'total' => $price * $item['quantity'],
                ]);

                // Decrement GudangStock quantity
                $gudangStock->decrement('quantity', $item['quantity']);
                $gudangStock->save();

                // Update the stock table with the total stock quantity across all warehouses
                $stock->stock = GudangStock::where('stocks_id', $stock->id)->sum('quantity');
                $stock->save();

                $totalAmount += $item['quantity'] * $price;
            }
        });

        // Handle insufficient stock items
        if (!empty($insufficientStockItems)) {
            $errorMessages = [];
            foreach ($insufficientStockItems as $item) {
                $errorMessages[] = "{$item['stock_name']}: Required {$item['required_quantity']} but only {$item['available_quantity']} available in the selected warehouse.";
            }
            return redirect()->back()->withErrors(['items' => $errorMessages])->withInput();
        }

        // Update the total for the penjualan
        $penjualan = Penjualan::findOrFail($penjualanId);
        $penjualan->total = $totalAmount;
        $penjualan->total_netto = $totalAmount - (($penjualan->diskon / 100) * $totalAmount);
        $dpp = $penjualan->total_netto / 1.11;
        $ppn = $penjualan->total_netto - $dpp;
        $penjualan->dpp = $dpp;
        $penjualan->ppn = $ppn;
        $penjualan->save();

        return redirect()->route('penjualan.index')->with('success', 'Items added and updated successfully');
    }

    public function edit($id)
    {
        $rincianpenjualan = RincianPenjualan::findOrFail($id);
        $penjualan = Penjualan::findOrFail($rincianpenjualan->penjualan_id);
        $stocks = Stock::all();
        $stockgudang = GudangStock::all();
        $gudangs = Gudang::all();
        return view('rincianpenjualan.edit', compact('rincianpenjualan', 'stocks', 'penjualan', 'gudangs'));
    }

    public function update(Request $request, $id)
    {
        $rincianpenjualan = RincianPenjualan::findOrFail($id);
        $stock = Stock::findOrFail($rincianpenjualan->stocks_id);
        $penjualan = Penjualan::findOrFail($rincianpenjualan->penjualan_id);

        // Validate request inputs
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
            'gudangs_id' => 'required|exists:gudangs,id',
        ]);

        $oldQuantity = $rincianpenjualan->quantity;
        $newQuantity = $validated['quantity'];
        $newGudangId = $validated['gudangs_id'];
        $price = $stock->jual;

        $insufficientStockItems = [];

            DB::transaction(function () use ($rincianpenjualan, $stock, $oldQuantity, $newQuantity, $newGudangId, $price, $penjualan) {
                $newGudangStock = GudangStock::where('stocks_id', $stock->id)
                                             ->where('gudangs_id', $newGudangId)
                                             ->lockForUpdate()
                                             ->firstOrFail();

                if (!$gudangStock || $gudangStock->quantity < $item['quantity']) {
                $insufficientStockItems[] = [
                    'stock_name' => $stock->name,
                    'required_quantity' => $item['quantity'],
                    'available_quantity' => $gudangStock ? $gudangStock->quantity : 0
                    ];
                }

                if ($rincianpenjualan->gudangs_id != $newGudangId) {
                    // Restore stock to the old warehouse
                    $oldGudangStock = GudangStock::where('stocks_id', $stock->id)
                                                 ->where('gudangs_id', $rincianpenjualan->gudangs_id)
                                                 ->lockForUpdate()
                                                 ->firstOrFail();
                    $oldGudangStock->increment('quantity', $oldQuantity);
                    $oldGudangStock->save();

                    // Deduct stock from the new warehouse
                    $newGudangStock->decrement('quantity', $newQuantity);
                    $newGudangStock->save();

                    // Update `gudangs_id` in RincianPenjualan
                    $rincianpenjualan->gudangs_id = $newGudangId;
                } else {
                    // Corrected logic: calculate the difference between the old and new quantity
                    $quantityDifference = $newQuantity - $oldQuantity;

                    // Adjust stock based on quantity difference
                    if ($quantityDifference > 0) {
                        $newGudangStock->decrement('quantity', $quantityDifference);
                    } else if ($quantityDifference < 0) {
                        $newGudangStock->increment('quantity', abs($quantityDifference));
                    }

                    $newGudangStock->save();
                }

                // Update RincianPenjualan record
                $rincianpenjualan->quantity = $newQuantity;
                $rincianpenjualan->price = $price;
                $rincianpenjualan->total = $newQuantity * $price;
                $rincianpenjualan->save();

                // Recalculate and update the total stock across all warehouses
                $stock->stock = GudangStock::where('stocks_id', $stock->id)->sum('quantity');
                $stock->save();

                // Recalculate totals for Penjualan
                $penjualan->total = $penjualan->rincianpenjualans()->sum('total');
                $penjualan->total_netto = $penjualan->total - (($penjualan->diskon / 100) * $penjualan->total);
                $dpp = $penjualan->total_netto / 1.11;
                $ppn = $penjualan->total_netto - $dpp;
                $penjualan->dpp = $dpp;
                $penjualan->ppn = $ppn;
                $penjualan->save();
            });

            // Handle insufficient stock items
        if (!empty($insufficientStockItems)) {
            $errorMessages = [];
            foreach ($insufficientStockItems as $item) {
                $errorMessages[] = "{$item['stock_name']}: Required {$item['required_quantity']} but only {$item['available_quantity']} available in the selected warehouse.";
            }
            return redirect()->back()->withErrors(['items' => $errorMessages])->withInput();
        }


        return redirect()->route('penjualan.index')->with('success', 'Item updated successfully');
    }

    public function destroy($id)
    {
        $rincianpenjualan = RincianPenjualan::findOrFail($id);

        // Restore the stock in the warehouse
        $gudangStock = GudangStock::where('stocks_id', $rincianpenjualan->stocks_id)
                                  ->where('gudangs_id', $rincianpenjualan->gudangs_id)
                                  ->firstOrFail();
        $gudangStock->increment('quantity', $rincianpenjualan->quantity);
        $gudangStock->save();

        // Recalculate and update the stock for the product
        $stock = Stock::findOrFail($rincianpenjualan->stocks_id);
        $stock->stock = GudangStock::where('stocks_id', $stock->id)->sum('quantity');
        $stock->save();

        // Delete the RincianPenjualan record
        $rincianpenjualan->delete();

        // Recalculate totals for Penjualan
        $penjualan = Penjualan::findOrFail($rincianpenjualan->penjualan_id);
        $penjualan->total = $penjualan->rincianpenjualans()->sum('total');
        $penjualan->total_netto = $penjualan->total - (($penjualan->diskon / 100) * $penjualan->total);
        $dpp = $penjualan->total_netto / 1.11;
        $ppn = $penjualan->total_netto - $dpp;
        $penjualan->dpp = $dpp;
        $penjualan->ppn = $ppn;
        $penjualan->save();

        return redirect()->back()->with('success', 'Item deleted successfully');
    }
}
