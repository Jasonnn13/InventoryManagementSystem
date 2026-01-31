<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\Ekonomi;
use App\Models\Stock;
use App\Models\Gudang;
use App\Models\GudangStock;
use App\Models\RincianPembelian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;  // For logging

class RincianPembelianController extends Controller
{
    public function index($pembelian_id)
    {
        $pembelian = Pembelian::findOrFail($pembelian_id);
        $rincianpembelians = RincianPembelian::where('pembelian_id', $pembelian_id)->orderBy('created_at', 'desc')->get();
        
        return view('rincianpembelian.index', compact('pembelian', 'rincianpembelians'));
    }


    public function create($pembelian_id)
    {
        $pembelian = Pembelian::findOrFail($pembelian_id);
        $stocks = Stock::where('suppliers_id', $pembelian->suppliers_id)->get();
        $gudangs = Gudang::all();
        return view('rincianpembelian.create', compact('pembelian', 'stocks', 'gudangs'));
    }


    public function store(Request $request)
    {
        // Validate the incoming request data
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'pembelian_id' => 'required|exists:pembelian,id',
            'items' => 'required|array',
            'items.new.*.name' => 'nullable|string',
            'items.new.*.quantity' => 'nullable|integer|min:0',
            'items.new.*.price' => 'nullable|numeric|min:0',
            'items.new.*.gudangs_id' => 'nullable|string',
            'items.new.*.kode' => 'nullable|string',
            'items.existing.*.name' => 'required|string|exists:stocks,name',
            'items.existing.*.quantity' => 'required|integer|min:0',
            'items.existing.*.price' => 'required|numeric|min:0',
        ]);
    
        $supplierId = $validated['supplier_id'];
        $pembelianId = $validated['pembelian_id'];
        $items = $validated['items'];
        
        // Handle new items
        if (isset($items['new']) && is_array($items['new'])) {
            foreach ($items['new'] as $newItem) {
                $name = $newItem['name'] ?? '';
                $quantity = $newItem['quantity'] ?? 0;
                $price = $newItem['price'] ?? 0;
                $kode = $newItem['kode'] ?? '';
    
                if (!empty($name) && $quantity > 0 && $price >= 0) {
                    // Create new stock
                    $stock = Stock::create([
                        'name' => $name,
                        'stock' => $quantity,
                        'suppliers_id' => $supplierId,
                        'kode' => $kode,
                        'beli' => $price,
                    ]);
                    
                    // Create new rincian pembelian
                    RincianPembelian::create([
                        'pembelian_id' => $pembelianId,
                        'stocks_id' => $stock->id,
                        'quantity' => $quantity,
                        'price' => $price,
                        'total' => $price * $quantity,
                    ]);

                    $pembelian = Pembelian::findOrFail($pembelianId);
                    $gudangId = $pembelian->gudangs_id;

                    GudangStock::create([
                        'stocks_id' => $stock->id,
                        'gudangs_id' => $gudangId,
                        'quantity' => $quantity,
                    ]);

                    $sum = GudangStock::where('stocks_id', $stock->id)
                    ->sum('quantity');

                    // Update stock quantity
                    $stock->stock = $sum;
                    $stock->save();
                }
            }
        }
    
        // Handle existing items
        if (isset($items['existing']) && is_array($items['existing'])) {
            foreach ($items['existing'] as $existingItem) {
                // Find the stock by name
                $stock = Stock::where('name', $existingItem['name'])->first();

                if ($stock) {
                    // Create a rincian pembelian entry for the existing item
                    RincianPembelian::create([
                        'pembelian_id' => $pembelianId,
                        'stocks_id' => $stock->id,
                        'quantity' => $existingItem['quantity'],
                        'price' => $existingItem['price'],
                        'total' => $existingItem['price'] * $existingItem['quantity'],
                    ]);

                    // Find the pembelian and retrieve the related gudang
                    $pembelian = Pembelian::findOrFail($pembelianId);
                    $gudangId = $pembelian->gudangs_id;

                    // Update the gudang stock for the specific gudang and stock combination
                    $gudangStock = GudangStock::where('stocks_id', $stock->id)
                                            ->where('gudangs_id', $gudangId)
                                            ->first();

                    if ($gudangStock) {
                        // Increment the existing gudang stock quantity
                        $gudangStock->increment('quantity', $existingItem['quantity']);
                    } else {
                        // Create a new gudang stock entry if it doesn't exist
                        GudangStock::create([
                            'stocks_id' => $stock->id,
                            'gudangs_id' => $gudangId,
                            'quantity' => $existingItem['quantity'],
                        ]);
                    }

                    // Update the stock purchase price (beli) and increment stock quantity
                    $stock->beli = $existingItem['price'];  // Update the beli field
                    $stock->stock += $existingItem['quantity'];  // Increment the stock quantity

                    // Save the updated stock
                    $stock->save();
                }
            }
        }

        
    
        return redirect()->route('rincianpembelian.index', $pembelianId)->with('success', 'Items added and updated successfully');
    }
    

    public function edit($id)
    {
        $rincianpembelian = RincianPembelian::findOrFail($id);
        $pembelian = Pembelian::findOrFail($rincianpembelian->pembelian_id);
        $stock = Stock::findOrFail($rincianpembelian->stocks_id);
        $gudangs = Gudang::all();
        return view('rincianpembelian.edit', compact('rincianpembelian', 'stock', 'pembelian', 'gudangs'));
    }


    public function update(Request $request, $id)
    {
        // Find the RincianPembelian record and related models
        $rincianPembelian = RincianPembelian::findOrFail($id);
        $stock = Stock::findOrFail($rincianPembelian->stocks_id);
        $pembelian = Pembelian::findOrFail($rincianPembelian->pembelian_id);
        $gudangId = $pembelian->gudangs_id;
    
        // Validate the incoming request data
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'kode' => 'nullable|string',  // Add kode validation
        ]);
    
        // Store the old quantity for stock adjustment
        $oldQuantity = $rincianPembelian->quantity;
    
        // Update the RincianPembelian record
        $rincianPembelian->quantity = $validated['quantity'];
        $rincianPembelian->price = $validated['price'];
        $rincianPembelian->total = $validated['quantity'] * $validated['price'];
        $rincianPembelian->save();
    
        // Update stock details (beli and kode)
        $stock->beli = $validated['price'];
        $stock->kode = $validated['kode'];
        
        // Update GudangStock accordingly
        $gudangStock = GudangStock::where('stocks_id', $stock->id)
            ->where('gudangs_id', $gudangId)
            ->firstOrFail();
    
        // Adjust the stock quantity based on the old and new quantities
        if ($validated['quantity'] > $oldQuantity) {
            $incrementAmount = $validated['quantity'] - $oldQuantity;
            $gudangStock->increment('quantity', $incrementAmount);
        } elseif ($validated['quantity'] < $oldQuantity) {
            $decrementAmount = $oldQuantity - $validated['quantity'];
            $gudangStock->decrement('quantity', $decrementAmount);
        }
    
        // Calculate and update the total stock quantity across all GudangStock entries
        $totalStockQuantity = GudangStock::where('stocks_id', $stock->id)->sum('quantity');
        $stock->stock = $totalStockQuantity;
        $stock->save();
    
        return redirect()->route('rincianpembelian.index', ['pembelian_id' => $rincianPembelian->pembelian_id])
            ->with('success', 'Rincian pembelian updated successfully.');
    }
    
    public function destroy($id)
    {
        $rincianPembelian = RincianPembelian::findOrFail($id);
        $stock = Stock::findOrFail($rincianPembelian->stocks_id);
        $pembelian = Pembelian::findOrFail($rincianPembelian->pembelian_id);
        $gudangId = $pembelian->gudangs_id;
    
        // Find the relevant GudangStock and decrement its quantity
        $gudangStock = GudangStock::where('stocks_id', $stock->id)
            ->where('gudangs_id', $gudangId)
            ->firstOrFail();
    
        // Decrement the GudangStock quantity
        $gudangStock->decrement('quantity', $rincianPembelian->quantity);
    
        // Calculate the new total stock quantity across all GudangStock entries
        $totalStockQuantity = GudangStock::where('stocks_id', $stock->id)->sum('quantity');
        $stock->stock = $totalStockQuantity;
        $stock->save();
    
        // Delete the RincianPembelian record
        $rincianPembelian->delete();
    
        return redirect()->route('rincianpembelian.index', ['pembelian_id' => $rincianPembelian->pembelian_id])
            ->with('success', 'Rincian pembelian deleted successfully.');
    }
    

}
