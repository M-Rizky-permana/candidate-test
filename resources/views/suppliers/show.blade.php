<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">{{ $supplier->name }}</h2>
                <p class="mt-1 text-sm text-gray-500">Supplier detail, nested CLT layups, and layers.</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <a href="{{ route('suppliers.layups.create', $supplier) }}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">+ Add Layup</a>
                <a href="{{ route('suppliers.import.form', $supplier) }}" class="rounded-xl border border-indigo-200 px-4 py-2 text-sm font-semibold text-indigo-700 hover:bg-indigo-50">Import JSON</a>
                <a href="{{ route('suppliers.export', $supplier) }}" class="rounded-xl border border-emerald-200 px-4 py-2 text-sm font-semibold text-emerald-700 hover:bg-emerald-50">Export JSON</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('suppliers.partials.flash')

            @if(session('import_summary'))
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 text-sm text-emerald-800">
                    <div class="font-semibold">Import summary</div>
                    <div class="mt-2 grid gap-2 sm:grid-cols-3 lg:grid-cols-6">
                        @foreach(session('import_summary') as $key => $value)
                            <div class="rounded-xl bg-white/80 p-3">
                                <div class="text-xs uppercase tracking-wide text-emerald-700">{{ str_replace('_', ' ', $key) }}</div>
                                <div class="text-lg font-bold">{{ $value }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <div class="grid gap-4 md:grid-cols-3">
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-sm font-medium text-gray-500">Supplier</p>
                    <p class="mt-2 text-2xl font-bold text-gray-900">{{ $supplier->name }}</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-sm font-medium text-gray-500">Layups</p>
                    <p class="mt-2 text-2xl font-bold text-indigo-700">{{ $supplier->layups_count }}</p>
                </div>
                <div class="rounded-2xl bg-white p-5 shadow-sm ring-1 ring-gray-200">
                    <p class="text-sm font-medium text-gray-500">Layers</p>
                    <p class="mt-2 text-2xl font-bold text-violet-700">{{ $supplier->layers_count }}</p>
                </div>
            </div>

            <div class="space-y-5">
                @forelse($supplier->layups as $layup)
                    <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
                        <div class="flex flex-col gap-3 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-violet-50 px-6 py-5 md:flex-row md:items-center md:justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">{{ $layup->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $layup->layers_count }} layers</p>
                            </div>
                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('layups.layers.create', $layup) }}" class="rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white hover:bg-indigo-700">+ Layer</a>
                                <a href="{{ route('layups.edit', $layup) }}" class="rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Edit</a>
                                <form method="POST" action="{{ route('layups.destroy', $layup) }}" onsubmit="return confirm('Delete this layup and all layers?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-rose-200 bg-white px-3 py-2 text-sm font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </div>

                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">
                                    <tr>
                                        <th class="px-6 py-3">Order</th>
                                        <th class="px-6 py-3">Thickness</th>
                                        <th class="px-6 py-3">Width</th>
                                        <th class="px-6 py-3">Angle</th>
                                        <th class="px-6 py-3 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 bg-white">
                                    @forelse($layup->layers as $layer)
                                        <tr>
                                            <td class="px-6 py-4 font-semibold text-gray-900">#{{ $layer->layer_order }}</td>
                                            <td class="px-6 py-4 text-gray-700">{{ number_format((float) $layer->thickness, 2) }}</td>
                                            <td class="px-6 py-4 text-gray-700">{{ number_format((float) $layer->width, 2) }}</td>
                                            <td class="px-6 py-4 text-gray-700">{{ number_format((float) $layer->angle, 2) }}°</td>
                                            <td class="px-6 py-4">
                                                <div class="flex justify-end gap-2">
                                                    <a href="{{ route('layers.edit', $layer) }}" class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50">Edit</a>

<form method="POST" action="{{ route('layers.destroy', $layer) }}" onsubmit="return confirm('Delete this layer?')">
    @csrf
    @method('DELETE')
    <button type="submit" class="rounded-lg border border-rose-200 px-3 py-1.5 text-xs font-medium text-rose-700 hover:bg-rose-50">Delete</button>
</form>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">No layers yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty
                    <div class="rounded-2xl bg-white px-6 py-12 text-center shadow-sm ring-1 ring-gray-200">
                        <p class="text-sm text-gray-500">No layups yet for this supplier.</p>
                        <a href="{{ route('suppliers.layups.create', $supplier) }}" class="mt-4 inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create layup</a>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
