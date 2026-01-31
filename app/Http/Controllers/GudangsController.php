<?php

namespace App\Http\Controllers;

use App\Models\Gudang;
use Illuminate\Http\Request;

class GudangsController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        // Fetch Gudangs based on search input
        $gudangs = Gudang::query()
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc') // Order by newest created_at
            ->paginate(10);

        return view('gudangs.index', compact('gudangs', 'search'));
    }

    public function create()
    {
        return view('gudangs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Gudang::create($request->all());

        return redirect()->route('gudangs.index')
                         ->with('success', 'Gudang created successfully.');
    }

    public function edit(Gudang $gudang)
    {
        return view('gudangs.edit', compact('gudang'));
    }

    public function update(Request $request, Gudang $gudang)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $gudang->update($request->all());

        return redirect()->route('gudangs.index')
                         ->with('success', 'Gudang updated successfully.');
    }

    public function destroy(Gudang $gudang)
    {
        $gudang->delete();

        return redirect()->route('gudangs.index')
                         ->with('success', 'Gudang deleted successfully.');
    }
}
