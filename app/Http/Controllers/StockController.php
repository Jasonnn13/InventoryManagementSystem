<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\Stock;
use App\Models\Gudang;
use App\Models\GudangStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
    
        // Fetch stocks with related stock and gudang details, applying search filters if provided
        $stocks = GudangStock::with(['stock', 'gudang'])
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
            ->paginate(10);
    
        $stocks->getCollection()->sortByDesc(function ($item) {
            return $item->stock->created_at;
        });
    
        return view('stocks.index', compact('stocks', 'search'));
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
        $stock = GudangStock::with('stock')->findOrFail($id); // Load the related Stock
        return view('stocks.edit', compact('stock'));
    }
    
    
    public function update(Request $request, $id)
    {
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
            ->get(['id', 'name', 'kode']);
        
        $results = $stocks->map(function ($stock) {
            return [
                'label' => $stock->name, // Display name
                // 'value' => $stock->id,   // ID to be used in the hidden field
            ];
        });
        
        return response()->json($results);
    }

    private function mapGudangs($gudangs)
    {
        return collect($gudangs)->mapWithKeys(function ($gudangId) {
            // If you need to attach additional data to the pivot table, you can do so here.
            // For example, if you need to set the quantity for each gudang:
            return [$gudangId => ['quantity' => 1]]; // Replace '1' with the actual quantity or other data if needed
        })->toArray();
    }
}
