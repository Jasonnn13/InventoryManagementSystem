<?php

namespace App\Http\Controllers;

use App\Models\Pembelian;
use App\Models\RincianPembelian;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Gudang;
use App\Models\GudangStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PembelianController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $month = $request->input('month');
        $year = $request->input('year');
    
        $query = Pembelian::query()
            ->join('suppliers', 'pembelian.suppliers_id', '=', 'suppliers.id')
            ->select('pembelian.*');
    
        // Apply search filter
        if ($search) {
            $query->where('suppliers.name', 'like', "%{$search}%");
        }
    
        // Apply month/year filter
        if ($month && $year) {
            $query->whereYear('pembelian.created_at', $year)
                  ->whereMonth('pembelian.created_at', $month);
        }
    
        // Order by newest created_at
        $query->orderBy('pembelian.created_at', 'desc');
    
        // Get the results
        $pembelians = $query->paginate(10);
    
        return view('pembelian.index', compact('pembelians', 'search', 'month', 'year'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        $gudangs = Gudang::all();
        return view('pembelian.create', compact('suppliers', 'gudangs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'suppliers_id' => 'required|exists:suppliers,id',
            'total' => 'required|numeric|min:0',
            'gudangs_id' => 'required|exists:gudangs,id',
        ]);

        $userId = Auth::id();

        Log::info('Store Request Data:', $validated);

        $pembelian = Pembelian::create([
            'suppliers_id' => $validated['suppliers_id'],
            'total' => $validated['total'],
            'users_id' => $userId,
            'gudangs_id' => $validated['gudangs_id'],
        ]);

        // Redirect to rincian pembelian creation
        return redirect()->route('rincianpembelian.create', ['pembelian_id' => $pembelian->id]);
    }

    public function edit(Pembelian $pembelian)
    {
        $suppliers = Supplier::all();
        $gudangs = Gudang::all();
        return view('pembelian.edit', compact('pembelian', 'suppliers', 'gudangs'));
    }

    public function update(Request $request, Pembelian $pembelian)
    {
        $validated = $request->validate([
            'suppliers_id' => 'required|exists:suppliers,id',
            'gudangs_id' => 'required|exists:gudangs,id',
            'total' => 'required|numeric|min:0',
        ]);

        // Update pembelian
        $pembelian->update([
            'suppliers_id' => $validated['suppliers_id'],
            'gudangs_id' => $validated['gudangs_id'],
            'total' => $validated['total'],
        ]);

        // Update suppliers_id for related stock items
        $rincianPembelian = RincianPembelian::where('pembelian_id', $pembelian->id)->get();

        foreach ($rincianPembelian as $rincian) {
            $stock = Stock::findOrFail($rincian->stocks_id);
            $stock->suppliers_id = $validated['suppliers_id'];
            $stock->save();

            // Update GudangStock (ensure stock is linked to the correct gudang)
            $gudangStock = GudangStock::where('stocks_id', $rincian->stocks_id)
                                       ->where('gudangs_id', $validated['gudangs_id'])
                                       ->first();
            
            if ($gudangStock) {
                // If it exists, update it
                $gudangStock->gudangs_id = $validated['gudangs_id'];
                $gudangStock->save();
            } else {
                // If it doesn't exist, create a new record
                GudangStock::create([
                    'stocks_id' => $rincian->stocks_id,
                    'gudangs_id' => $validated['gudangs_id'],
                    'quantity' => $rincian->quantity,
                ]);
            }
        }

        return redirect()->route('pembelian.index')
                         ->with('success', 'Pembelian updated successfully.');
    }

    public function destroy(Pembelian $pembelian)
    {
        $rincianPembelians = RincianPembelian::where('pembelian_id', $pembelian->id)->get();

        foreach ($rincianPembelians as $rincian) {
            $stock = Stock::findOrFail($rincian->stocks_id);

            // Update the stock quantity
            $stock->stock -= $rincian->quantity;
            $stock->save();

            // Update GudangStock (remove stock from warehouse)
            $gudangStock = GudangStock::where('stocks_id', $rincian->stocks_id)
                                       ->where('gudangs_id', $pembelian->gudangs_id)
                                       ->first();

            if ($gudangStock) {
                $gudangStock->quantity -= $rincian->quantity;
                $gudangStock->save();
            }

            // Delete each rincian pembelian record
            $rincian->delete();
        }

        // Delete the pembelian itself
        $pembelian->delete();

        return redirect()->route('pembelian.index')
                        ->with('success', 'Pembelian and associated rincian pembelian deleted successfully.');
    }
}
