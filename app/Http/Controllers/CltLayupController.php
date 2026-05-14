<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLayupRequest;
use App\Http\Requests\UpdateLayupRequest;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CltLayupController extends Controller
{
    public function create(Supplier $supplier): View
    {
        return view('layups.create', compact('supplier'));
    }

    public function store(StoreLayupRequest $request, Supplier $supplier): RedirectResponse
    {
        $supplier->layups()->create($request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'CLT layup created successfully.');
    }

    public function show(CltLayup $layup)
    {
        $layup->load(['supplier', 'layers']);

        return view('layups.show', compact('layup'));
    }
    public function edit(Supplier $supplier, CltLayup $layup): View
    {
        $this->ensureLayupBelongsToSupplier($supplier, $layup);

        return view('layups.edit', compact('supplier', 'layup'));
    }

    public function update(UpdateLayupRequest $request, Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->ensureLayupBelongsToSupplier($supplier, $layup);

        $layup->update($request->validated());

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'CLT layup updated successfully.');
    }

    public function destroy(Supplier $supplier, CltLayup $layup): RedirectResponse
    {
        $this->ensureLayupBelongsToSupplier($supplier, $layup);

        $layup->delete();

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'CLT layup deleted successfully.');
    }

    private function ensureLayupBelongsToSupplier(Supplier $supplier, CltLayup $layup): void
    {
        abort_unless($layup->supplier_id === $supplier->id, 404);
    }
}
