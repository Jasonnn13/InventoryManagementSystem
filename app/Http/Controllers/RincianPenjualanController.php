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
            'items.*.gudangs_id' => 'required|exists:gudangs,id',
        ]);

        $penjualanId = $validated['penjualan_id'];
        $items = $validated['items'];
        try {
            DB::transaction(function () use ($items, $penjualanId) {
                $preparedItems = [];

                // Validate all requested items first; if any fails, rollback everything.
                foreach ($items as $item) {
                    $stock = Stock::where('name', $item['name'])->first();

                    if (!$stock) {
                        throw new \RuntimeException("Item {$item['name']} tidak ditemukan.");
                    }

                    $gudangId = $item['gudangs_id'];
                    $gudangStock = GudangStock::where('stocks_id', $stock->id)
                        ->where('gudangs_id', $gudangId)
                        ->lockForUpdate()
                        ->first();

                    if (!$gudangStock || $gudangStock->quantity < $item['quantity']) {
                        $available = $gudangStock ? $gudangStock->quantity : 0;
                        throw new \RuntimeException("{$stock->name}: Butuh {$item['quantity']} tapi stok di gudang terpilih tinggal {$available}.");
                    }

                    $preparedItems[] = [
                        'stock' => $stock,
                        'gudangStock' => $gudangStock,
                        'gudang_id' => $gudangId,
                        'quantity' => $item['quantity'],
                    ];
                }

                foreach ($preparedItems as $prepared) {
                    $stock = $prepared['stock'];
                    $gudangStock = $prepared['gudangStock'];
                    $quantity = $prepared['quantity'];
                    $price = $stock->jual;

                    RincianPenjualan::create([
                        'penjualan_id' => $penjualanId,
                        'stocks_id' => $stock->id,
                        'gudangs_id' => $prepared['gudang_id'],
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $price * $quantity,
                    ]);

                    $gudangStock->decrement('quantity', $quantity);
                    $gudangStock->save();

                    $stock->stock = GudangStock::where('stocks_id', $stock->id)->sum('quantity');
                    $stock->save();
                }

                $penjualan = Penjualan::findOrFail($penjualanId);
                $totalAmount = RincianPenjualan::where('penjualan_id', $penjualanId)->sum('total');

                $penjualan->total = $totalAmount;
                $penjualan->total_netto = $totalAmount - (($penjualan->diskon / 100) * $totalAmount);

                if ($penjualan->tipe_ppn === 'PPN') {
                    $dpp = $penjualan->total_netto / 1.11;
                    $ppn = $penjualan->total_netto - $dpp;
                } else {
                    $dpp = $penjualan->total_netto;
                    $ppn = 0;
                }

                $penjualan->dpp = $dpp;
                $penjualan->ppn = $ppn;
                $penjualan->save();
            });
        } catch (\RuntimeException $e) {
            return redirect()->back()->withErrors(['items' => [$e->getMessage()]])->withInput();
        }

        return redirect('/penjualan')->with('success', 'Items added and updated successfully');
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

        $errorMessage = null;

        DB::transaction(function () use (
            $rincianpenjualan,
            $stock,
            $oldQuantity,
            $newQuantity,
            $newGudangId,
            $price,
            $penjualan,
            &$errorMessage
        ) {
            $newGudangStock = GudangStock::where('stocks_id', $stock->id)
                ->where('gudangs_id', $newGudangId)
                ->lockForUpdate()
                ->first();

            if (!$newGudangStock) {
                $errorMessage = "{$stock->name}: Stok tidak tersedia di gudang yang dipilih.";
                return;
            }

            if ($rincianpenjualan->gudangs_id != $newGudangId) {
                $availableQuantity = $newGudangStock->quantity;

                if ($availableQuantity < $newQuantity) {
                    $errorMessage = "{$stock->name}: Butuh {$newQuantity} tapi stok di gudang terpilih tinggal {$availableQuantity}.";
                    return;
                }

                $oldGudangStock = GudangStock::where('stocks_id', $stock->id)
                    ->where('gudangs_id', $rincianpenjualan->gudangs_id)
                    ->lockForUpdate()
                    ->first();

                if ($oldGudangStock) {
                    $oldGudangStock->increment('quantity', $oldQuantity);
                    $oldGudangStock->save();
                }

                $newGudangStock->decrement('quantity', $newQuantity);
                $newGudangStock->save();

                $rincianpenjualan->gudangs_id = $newGudangId;
            } else {
                $quantityDifference = $newQuantity - $oldQuantity;

                if ($quantityDifference > 0) {
                    if ($newGudangStock->quantity < $quantityDifference) {
                        $errorMessage = "{$stock->name}: Butuh {$newQuantity} tapi stok di gudang terpilih tinggal {$newGudangStock->quantity}.";
                        return;
                    }
                    $newGudangStock->decrement('quantity', $quantityDifference);
                } elseif ($quantityDifference < 0) {
                    $newGudangStock->increment('quantity', abs($quantityDifference));
                }

                $newGudangStock->save();
            }

            $rincianpenjualan->quantity = $newQuantity;
            $rincianpenjualan->price = $price;
            $rincianpenjualan->total = $newQuantity * $price;
            $rincianpenjualan->save();

            $stock->stock = GudangStock::where('stocks_id', $stock->id)->sum('quantity');
            $stock->save();

            $penjualan->total = $penjualan->rincianpenjualans()->sum('total');
            $penjualan->total_netto = $penjualan->total - (($penjualan->diskon / 100) * $penjualan->total);
            if ($penjualan->tipe_ppn === 'PPN') {
                $dpp = $penjualan->total_netto / 1.11;
                $ppn = $penjualan->total_netto - $dpp;
            } else {
                $dpp = $penjualan->total_netto;
                $ppn = 0;
            }
            $penjualan->dpp = $dpp;
            $penjualan->ppn = $ppn;
            $penjualan->save();
        });

        if ($errorMessage) {
            return redirect()->back()->withErrors(['items' => [$errorMessage]])->withInput();
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
        if ($penjualan->tipe_ppn === 'PPN') {
            $dpp = $penjualan->total_netto / 1.11;
            $ppn = $penjualan->total_netto - $dpp;
        } else {
            $dpp = $penjualan->total_netto;
            $ppn = 0;
        }
        $penjualan->dpp = $dpp;
        $penjualan->ppn = $ppn;
        $penjualan->save();

        return redirect()->back()->with('success', 'Item deleted successfully');
    }
}
