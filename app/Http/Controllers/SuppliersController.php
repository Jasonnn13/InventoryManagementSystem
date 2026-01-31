<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SuppliersController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Fetch suppliers based on search input
        $suppliers = Supplier::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                             ->orWhere('contact_information', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc') // Order by newest created_at
            ->paginate(10);

        return view('suppliers.index', compact('suppliers', 'search'));
    }
    
    public function create()
    {
        return view('suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_information' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $supplier = new Supplier();
        $supplier->name = $request->input('name');
        $supplier->contact_information = $request->input('contact_information');
        $supplier->address = $request->input('address');
        $supplier->save();

        return redirect()->route('suppliers.index')->with('success', 'Supplier created successfully.');
    }

    public function edit($id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_information' => 'required|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $supplier = Supplier::findOrFail($id);
        $supplier->update([
            'name' => $request->input('name'),
            'contact_information' => $request->input('contact_information'),
            'address' => $request->input('address'),
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier updated successfully.');
    }


    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);
        $supplier->delete();

        return redirect()->route('suppliers.index')->with('success', 'Supplier deleted successfully.');
    }
}
