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
use Illuminate\Support\Facades\DB;
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

        $oldGudangId = $pembelian->gudangs_id;
        $newGudangId = (int) $validated['gudangs_id'];
        $gudangChanged = $oldGudangId !== $newGudangId;

        DB::transaction(function () use ($pembelian, $validated, $oldGudangId, $newGudangId, $gudangChanged) {
            // Update pembelian header
            $pembelian->update([
                'suppliers_id' => $validated['suppliers_id'],
                'gudangs_id'   => $newGudangId,
                'total'        => $validated['total'],
            ]);

            $rincianPembelians = RincianPembelian::where('pembelian_id', $pembelian->id)->get();

            foreach ($rincianPembelians as $rincian) {
                // Update the linked supplier on the stock
                $stock = Stock::findOrFail($rincian->stocks_id);
                $stock->suppliers_id = $validated['suppliers_id'];
                $stock->save();

                if ($gudangChanged) {
                    // Decrement (or remove) the old gudang's stock for this item
                    $oldGudangStock = GudangStock::where('stocks_id', $rincian->stocks_id)
                        ->where('gudangs_id', $oldGudangId)
                        ->lockForUpdate()
                        ->first();

                    if ($oldGudangStock) {
                        if ($oldGudangStock->quantity <= $rincian->quantity) {
                            $oldGudangStock->delete();
                        } else {
                            $oldGudangStock->decrement('quantity', $rincian->quantity);
                        }
                    }

                    // Increment (or create) the new gudang's stock for this item
                    $newGudangStock = GudangStock::where('stocks_id', $rincian->stocks_id)
                        ->where('gudangs_id', $newGudangId)
                        ->lockForUpdate()
                        ->first();

                    if ($newGudangStock) {
                        $newGudangStock->increment('quantity', $rincian->quantity);
                    } else {
                        GudangStock::create([
                            'stocks_id'  => $rincian->stocks_id,
                            'gudangs_id' => $newGudangId,
                            'quantity'   => $rincian->quantity,
                        ]);
                    }

                    // Recalculate the canonical stock total across all gudangs
                    $stock->stock = GudangStock::where('stocks_id', $stock->id)->sum('quantity');
                    $stock->save();
                }
            }
        });

        return redirect()->route('pembelian.index')
                         ->with('success', 'Pembelian updated successfully.');
    }

    public function destroy(Pembelian $pembelian)
    {
        DB::transaction(function () use ($pembelian) {
            $rincianPembelians = RincianPembelian::where('pembelian_id', $pembelian->id)
                ->lockForUpdate()
                ->get();

            foreach ($rincianPembelians as $rincian) {
                // Decrement the gudang that received this stock at purchase time.
                // pembelian->gudangs_id is the canonical source; update() keeps it in sync.
                $gudangStock = GudangStock::where('stocks_id', $rincian->stocks_id)
                    ->where('gudangs_id', $pembelian->gudangs_id)
                    ->lockForUpdate()
                    ->first();

                if ($gudangStock) {
                    if ($gudangStock->quantity <= $rincian->quantity) {
                        // Quantity would hit zero — remove the row entirely
                        $gudangStock->delete();
                    } else {
                        $gudangStock->decrement('quantity', $rincian->quantity);
                    }
                }

                // Recalculate canonical stock total from remaining GudangStock rows
                $stock = Stock::find($rincian->stocks_id);
                if ($stock) {
                    $stock->stock = GudangStock::where('stocks_id', $stock->id)->sum('quantity');
                    $stock->save();
                }

                $rincian->delete();
            }

            $pembelian->delete();
        });

        return redirect()->route('pembelian.index')
            ->with('success', 'Pembelian and associated rincian pembelian deleted successfully.');
    }
}
