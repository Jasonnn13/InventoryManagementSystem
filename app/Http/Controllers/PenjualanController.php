<?php

namespace App\Http\Controllers;

use App\Models\Penjualan;
use App\Models\RincianPenjualan;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\Customer;
use App\Models\Gudang;
use App\Models\GudangStock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PenjualanController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $month = $request->input('month');
        $year = $request->input('year');
        $status = $request->input('status');
    
        $query = Penjualan::query()
            ->join('customers', 'penjualan.customers_id', '=', 'customers.id')
            ->select('penjualan.*'); // Ensure only columns from penjualan are selected
    
        // Apply search filter
        if ($search) {
            $query->where('customers.name', 'like', "%{$search}%");
        }
    
        // Apply month/year filter if provided
        if ($month && $year) {
            $query->whereYear('penjualan.created_at', $year)
                  ->whereMonth('penjualan.created_at', $month);
        }

        if($status){
            $query->where('penjualan.status', $status);
        }

        $query->orderBy('penjualan.created_at', 'desc');
    
        // Get the results
        $penjualans = $query->paginate(10);

    
        return view('penjualan.index', compact('penjualans', 'search', 'month', 'year', 'status'));
    }
    
    

    public function create()
    {
        $suppliers = Supplier::all();
        $customers = Customer::all();
        return view('penjualan.create', compact('suppliers', 'customers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customers_id' => 'required|exists:customers,id',
            'status' => 'required|string',
            'tipe' => 'required|in:Cash,Piutang',
            'tipe_ppn' => 'required|in:PPN,Non PPN',
            'sales' => 'required|string',
            'tenggat_waktu' => 'required|date',
            'diskon' => 'nullable|integer|between:0,100', // Allow nullable values if not provided
        ]);
    
        $userId = Auth::id(); // Ensure you get the authenticated user ID
    
        // Debugging: Log request data
        Log::info('Store Request Data:', $validated);
    
        $penjualan = Penjualan::create([
            'customers_id' => $validated['customers_id'],
            'total' => 0, // Total will be updated later
            'status' => $validated['status'],
            'tipe' => $validated['tipe'],
            'sales' => $validated['sales'],
            'tenggat_waktu' => $validated['tenggat_waktu'],
            'users_id' => $userId, // Ensure this is set
            'ppn' => 0, // PPN will be updated later
            'dpp' => 0, // dpp will be updated later
            'total_netto' => 0, 
            'diskon' => $validated['diskon'], 
            'tipe_ppn' => $validated['tipe_ppn'],
        ]);

        
        


        // Redirect to create rincianpenjualan page with the correct penjualan_id
        return redirect()->route('rincianpenjualan.create', ['penjualan_id' => $penjualan->id]);
    }
    


    public function edit(Penjualan $penjualan)
    {
        $suppliers = Supplier::all();
        $customers = Customer::all();
        return view('penjualan.edit', compact('penjualan', 'suppliers', 'customers'));
    }

    public function update(Request $request, Penjualan $penjualan)
    {
        $validated = $request->validate([
            'customers_id' => 'required|exists:customers,id',
            'status' => 'required|string',
            'tipe' => 'required|in:Cash,Piutang',
            'tipe_ppn' => 'required|in:PPN,Non PPN',
            'sales' => 'required|string',
            'tenggat_waktu' => 'required|date',
            'diskon' => 'nullable|integer|between:0,100', // Allow nullable values if not provided
        ]);

    
        $penjualan->update([
            'customers_id' => $validated['customers_id'],
            'status' => $validated['status'],
            'tipe' => $validated['tipe'],
            'tipe_ppn' => $validated['tipe_ppn'],
            'sales' => $validated['sales'],
            'tenggat_waktu' => $validated['tenggat_waktu'],
            'diskon' => $validated['diskon'], // Assign the validated diskon value
        ]);

        $penjualan = Penjualan::find($penjualan->id);
        $penjualan->total_netto = $penjualan->total - (($penjualan->diskon/100) * $penjualan->total);
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

    
        return redirect()->route('penjualan.index')
                         ->with('success', 'Penjualan updated successfully.');
    }
    

    public function destroy(Penjualan $penjualan)
    {
        // Get all rincian penjualan associated with the penjualan
        $rincianpenjualans = RincianPenjualan::where('penjualan_id', $penjualan->id)->get();
        
        // Update the stocks and gudang_stock tables based on the items being deleted
        foreach ($rincianpenjualans as $rincian) {
            // Find the stock by the ID from rincian penjualan
            $stock = Stock::find($rincian->stocks_id);
            
            if ($stock) {
                // Update the GudangStock quantity first
                $gudangStock = GudangStock::where('stocks_id', $rincian->stocks_id)
                                          ->where('gudangs_id', $rincian->gudangs_id)  // Use rincian's gudangs_id
                                          ->first();
                
                if ($gudangStock) {
                    $gudangStock->increment('quantity', $rincian->quantity);  // Increment the quantity in the warehouse
                    $gudangStock->save();
                }
    
                // Update the overall stock table
                $stock->stock = GudangStock::where('stocks_id', $rincian->stocks_id)->sum('quantity');
                $stock->save();
            }
        }
    
        // Delete all rincian penjualan with the same penjualan id
        RincianPenjualan::where('penjualan_id', $penjualan->id)->delete();
    
        // Delete the penjualan
        $penjualan->delete();
    
        return redirect()->route('penjualan.index')
                        ->with('success', 'Penjualan and associated rincian penjualan deleted successfully.');
    }
        



}
