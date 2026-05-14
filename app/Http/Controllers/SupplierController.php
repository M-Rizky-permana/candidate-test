<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSupplierRequest;
use App\Http\Requests\UpdateSupplierRequest;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SupplierController extends Controller
{
    public function index(): View
    {
        $suppliers = Supplier::query()
            ->withCount(['layups', 'layers'])
            ->latest()
            ->paginate(10);

        return view('suppliers.index', compact('suppliers'));
    }

    public function create(): View
    {
        return view('suppliers.create');
    }

    public function store(StoreSupplierRequest $request): RedirectResponse
    {
        $supplier = Supplier::create($request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Supplier created successfully.');
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load([
            'layups' => fn ($query) => $query
                ->with(['layers' => fn ($layerQuery) => $layerQuery->orderBy('layer_order')])
                ->withCount('layers')
                ->orderBy('name'),
        ])->loadCount(['layups', 'layers']);

        return view('suppliers.show', compact('supplier'));
    }

    public function edit(Supplier $supplier): View
    {
        return view('suppliers.edit', compact('supplier'));
    }

    public function update(UpdateSupplierRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Supplier updated successfully.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete();

        return redirect()
            ->route('suppliers.index')
            ->with('success', 'Supplier deleted successfully.');
    }
}
