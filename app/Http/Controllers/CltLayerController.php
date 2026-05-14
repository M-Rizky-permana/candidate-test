<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLayerRequest;
use App\Http\Requests\UpdateLayerRequest;
use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Http\Request;

class CltLayerController extends Controller
{
     public function index(CltLayup $layup)
    {
        return view('layers.index', compact('layup'));
    }

    public function create(CltLayup $layup)
    {
        return view('layers.create', compact('layup'));
    }

    public function store(Request $request, CltLayup $layup)
    {
        $validated = $request->validate([
            'layer_order' => ['required', 'integer', 'min:1'],
            'thickness' => ['required', 'numeric', 'min:0'],
            'width' => ['required', 'numeric', 'min:0'],
            'angle' => ['required', 'numeric'],
        ]);

        $layup->layers()->create($validated);

        return redirect()
            ->route('suppliers.show', $layup->supplier)
            ->with('success', 'Layer berhasil ditambahkan.');
    }

    public function show(CltLayer $layer)
    {
        return view('layers.show', compact('layer'));
    }

    public function edit(CltLayer $layer)
    {
        return view('layers.edit', compact('layer'));
    }

    public function update(Request $request, CltLayer $layer)
    {
        $validated = $request->validate([
            'layer_order' => ['required', 'integer', 'min:1'],
            'thickness' => ['required', 'numeric', 'min:0'],
            'width' => ['required', 'numeric', 'min:0'],
            'angle' => ['required', 'numeric'],
        ]);

        $layer->update($validated);

        return redirect()
            ->route('suppliers.show', $layer->layup->supplier)
            ->with('success', 'Layer berhasil diperbarui.');
    }

    public function destroy(CltLayer $layer)
    {
        $supplier = $layer->layup->supplier;

        $layer->delete();

        return redirect()
            ->route('suppliers.show', $supplier)
            ->with('success', 'Layer berhasil dihapus.');
    }
}
