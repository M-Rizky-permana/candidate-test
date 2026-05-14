<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold leading-tight text-gray-800">CLT Suppliers</h2>
                <p class="mt-1 text-sm text-gray-500">Manage Supplier → Layups → Layers hierarchy.</p>
            </div>
            <a href="{{ route('suppliers.create') }}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700">
                + New Supplier
            </a>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('suppliers.partials.flash')

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-gray-200">
                <div class="border-b border-gray-100 px-6 py-5">
                    <h3 class="text-base font-semibold text-gray-900">Supplier List</h3>
                    <p class="text-sm text-gray-500">Use export/import actions per supplier for JSON transfer.</p>
                </div>

                <div class="divide-y divide-gray-100">
                    @forelse ($suppliers as $supplier)
                        <div class="flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between">
                            <div>
                                <a href="{{ route('suppliers.show', $supplier) }}" class="text-lg font-semibold text-gray-900 hover:text-indigo-700">
                                    {{ $supplier->name }}
                                </a>
                                <div class="mt-2 flex flex-wrap gap-2 text-xs font-medium text-gray-600">
                                    <span class="rounded-full bg-indigo-50 px-3 py-1 text-indigo-700">{{ $supplier->layups_count }} layups</span>
                                    <span class="rounded-full bg-violet-50 px-3 py-1 text-violet-700">{{ $supplier->layers_count }} layers</span>
                                </div>
                            </div>

                            <div class="flex flex-wrap gap-2">
                                <a href="{{ route('suppliers.show', $supplier) }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">View</a>
                                <a href="{{ route('suppliers.edit', $supplier) }}" class="rounded-lg border border-gray-200 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Edit</a>
                                <a href="{{ route('suppliers.import.form', $supplier) }}" class="rounded-lg border border-indigo-200 px-3 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-50">Import</a>
                                <a href="{{ route('suppliers.export', $supplier) }}" class="rounded-lg border border-emerald-200 px-3 py-2 text-sm font-medium text-emerald-700 hover:bg-emerald-50">Export</a>
                                <form method="POST" action="{{ route('suppliers.destroy', $supplier) }}" onsubmit="return confirm('Delete this supplier and all related layups/layers?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="rounded-lg border border-rose-200 px-3 py-2 text-sm font-medium text-rose-700 hover:bg-rose-50">Delete</button>
                                </form>
                            </div>
                        </div>
                    @empty
                        <div class="px-6 py-12 text-center">
                            <p class="text-sm text-gray-500">No suppliers yet.</p>
                            <a href="{{ route('suppliers.create') }}" class="mt-4 inline-flex rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Create first supplier</a>
                        </div>
                    @endforelse
                </div>
            </div>

            {{ $suppliers->links() }}
        </div>
    </div>
</x-app-layout>
