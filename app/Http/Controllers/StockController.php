<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Stock;
use App\Models\Gudang;
use App\Models\GudangStock;
use App\Models\RincianPembelian;
use App\Models\RincianPenjualan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $gudangId = $request->input('gudang_id');
    
        // Fetch stocks with related stock and gudang details, applying search filters if provided
        $stocks = GudangStock::with(['stock', 'gudang'])
            ->when($gudangId, function ($query, $gudangId) {
                $query->where('gudangs_id', $gudangId);
            })
            ->when($search, function ($query, $search) {
                $query->whereHas('stock', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('kode', 'like', "%{$search}%");
                })
                ->orWhereHas('stock.supplier', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                })
                ->orWhereHas('gudang', function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);
    
        $gudangs = Gudang::orderBy('name')->get();

        return view('stocks.index', compact('stocks', 'search', 'gudangs', 'gudangId'));
    }
    
    
    
    

    public function create()
    {
        $suppliers = Supplier::all();
        $gudangs = Gudang::all();
        return view('stocks.create', compact('suppliers', 'gudangs'));
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'kode' => 'required|string|max:255|unique:stocks,kode',
            'stock' => 'required|integer|min:0',
            'jual' => 'nullable|numeric|min:0',
            'beli' => 'nullable|numeric|min:0',
            'suppliers_id' => 'required|exists:suppliers,id',
            'gudangs_id' => 'required|exists:gudangs,id',
        ]);


        
        $stock = Stock::create([
            'name' => $request->input('name'),
            'kode' => $request->input('kode'),
            'stock' => 0,
            'jual' => $request->input('jual'),
            'beli' => $request->input('beli'),
            'suppliers_id' => $request->input('suppliers_id'),
        ]);

        GudangStock::create([
            'stocks_id' => $stock->id,
            'gudangs_id' => $request->input('gudangs_id'),
            'quantity' => $request->input('stock'),
        ]);

        $sum = GudangStock::where('stocks_id', $stock->id)
        ->sum('quantity');

        $stock2 = Stock::findOrFail($stock->id);
        $stock2->update([
            'stock' => $sum,
        ]);
        
    
        return redirect()->route('stocks.index')->with('success', 'Stock created successfully.');
    }
    
    public function edit($id)
    {
        $this->authorizeOwner();

        $stock = GudangStock::with('stock')->findOrFail($id); // Load the related Stock
        return view('stocks.edit', compact('stock'));
    }
    
    
    public function update(Request $request, $id)
    {
        $this->authorizeOwner();

        $request->validate([
            'name' => 'required|string|max:255',
            'kode' => 'required|string|max:255',
            'stock' => 'required|integer|min:0',
            'jual' => 'nullable|numeric|min:0',
            'beli' => 'nullable|numeric|min:0',
        ]);
    
        // Find GudangStock and update the quantity
        $gs = GudangStock::findOrFail($id);
        $gs->update([
            'quantity' => $request->input('stock'),
        ]);
        
        // Find the associated Stock
        $stock = Stock::findOrFail($gs->stocks_id);
    
        // Calculate the new sum of quantities
        $sum = GudangStock::where('stocks_id', $stock->id)
            ->sum('quantity');
    
        // Update the Stock record
        $stock->update([
            'name' => $request->input('name'),
            'kode' => $request->input('kode'),
            'stock' => $sum,
            'jual' => $request->input('jual'),
            'beli' => $request->input('beli'),
        ]);
    
        return redirect()->route('stocks.index')->with('success', 'Stock updated successfully.');
    }
    

    public function destroy($id)
    {
        $this->authorizeOwner();

        $stock = GudangStock::findOrFail($id);
        Stock::findOrFail($stock->stocks_id)->decrement('stock', $stock->quantity);
        $stock->delete();

        if(GudangStock::where('stocks_id', $stock->stocks_id)->count() == 0){
            Stock::findOrFail($stock->stocks_id)->delete();
        }

        return redirect()->route('stocks.index')->with('success', 'Stock deleted successfully.');
    }

    public function autocomplete(Request $request)
    {
        $search = $request->input('term');
        
        $stocks = Stock::query()
            ->where('name', 'like', "%{$search}%")
            ->orWhere('kode', 'like', "%{$search}%")
            ->limit(10)
            ->get(['id', 'name', 'kode', 'beli', 'stock']);
        
        $results = $stocks->map(function ($stock) {
            return [
                'id' => $stock->id,
                'label' => $stock->name, // Display name
                'price' => (int) ($stock->beli ?? 0),
                'quantity' => (int) ($stock->stock ?? 0),
            ];
        });
        
        return response()->json($results);
    }

    public function availableGudangs(Stock $stock)
    {
        $gudangs = GudangStock::with('gudang')
            ->where('stocks_id', $stock->id)
            ->where('quantity', '>', 0)
            ->orderByDesc('quantity')
            ->get()
            ->map(function ($gudangStock) {
                return [
                    'id' => $gudangStock->gudang->id,
                    'name' => $gudangStock->gudang->name,
                    'quantity' => (int) $gudangStock->quantity,
                ];
            })
            ->values();

        return response()->json($gudangs);
    }

    public function history($stock)
    {
        $stock = Stock::with('supplier')->findOrFail($stock);

        $gudangStocks = GudangStock::with('gudang')
            ->where('stocks_id', $stock->id)
            ->orderByDesc('quantity')
            ->get();

        $purchaseHistories = RincianPembelian::with(['pembelian.supplier', 'pembelian.user'])
            ->where('stocks_id', $stock->id)
            ->orderByDesc('created_at')
            ->get();

        $salesHistories = RincianPenjualan::with(['penjualan.customer', 'penjualan.user', 'gudang'])
            ->where('stocks_id', $stock->id)
            ->orderByDesc('created_at')
            ->get();

        $transactionHistories = collect()
            ->merge($purchaseHistories->map(function ($history) {
                return [
                    'tanggal' => $history->created_at,
                    'jenis' => 'Pembelian',
                    'mitra' => $history->pembelian->supplier->name ?? '-',
                    'gudang' => $history->pembelian->gudang->name ?? '-',
                    'qty' => $history->quantity,
                    'harga' => $history->price,
                    'total' => $history->total,
                    'status' => '-',
                ];
            }))
            ->merge($salesHistories->map(function ($history) {
                return [
                    'tanggal' => $history->created_at,
                    'jenis' => 'Penjualan',
                    'mitra' => $history->penjualan->customer->name ?? '-',
                    'gudang' => $history->gudang->name ?? '-',
                    'qty' => $history->quantity,
                    'harga' => $history->price,
                    'total' => $history->total,
                    'status' => $history->penjualan->status ?? '-',
                ];
            }))
            ->sortByDesc('tanggal')
            ->values();

        return view('stocks.detail', compact('stock', 'gudangStocks', 'transactionHistories'));
    }

    private function mapGudangs($gudangs)
    {
        return collect($gudangs)->mapWithKeys(function ($gudangId) {
            // If you need to attach additional data to the pivot table, you can do so here.
            // For example, if you need to set the quantity for each gudang:
            return [$gudangId => ['quantity' => 1]]; // Replace '1' with the actual quantity or other data if needed
        })->toArray();
    }

    private function authorizeOwner(): void
    {
        if (!Auth::check() || Auth::user()->level < User::LEVEL_OWNER) {
            abort(403, 'Hanya owner yang dapat mengubah atau menghapus stok.');
        }
    }
}
