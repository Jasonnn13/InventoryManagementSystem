<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Supplier;
use Illuminate\Http\Request;

class CustomersController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type', 'customer'); // Default to customer

        if ($type === 'supplier') {
            // Fetch suppliers only
            $data = Supplier::query()
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', "%{$search}%")
                                 ->orWhere('contact_information', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            $partners = $data;
            $pageTitle = 'Supplier';
            $emptyMessage = 'Belum ada supplier.';
        } else {
            // Fetch customers by default
            $data = Customer::query()
                ->when($search, function ($query, $search) {
                    return $query->where('name', 'like', "%{$search}%")
                                 ->orWhere('contact_information', 'like', "%{$search}%");
                })
                ->orderBy('created_at', 'desc')
                ->paginate(10);
            
            $partners = $data;
            $pageTitle = 'Pelanggan';
            $emptyMessage = 'Belum ada pelanggan.';
        }

        return view('customers.index', compact('partners', 'search', 'type', 'pageTitle', 'emptyMessage'));
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_information' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        Customer::create($request->all());

        return $this->redirectToMitraIndex($request, 'Customer created successfully.');
    }

    public function edit(Customer $customer)
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'contact_information' => 'nullable|string|max:255',
            'address' => 'required|string|max:255',
        ]);

        $customer->update($request->all());

        return $this->redirectToMitraIndex($request, 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return $this->redirectToMitraIndex(request(), 'Customer deleted successfully.');
    }

    private function redirectToMitraIndex(Request $request, string $message)
    {
        $query = array_filter([
            'type' => $request->input('type', 'customer'),
            'search' => $request->input('search'),
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        return redirect()->route('customers.index', $query)
                         ->with('success', $message);
    }
}
