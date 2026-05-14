<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Edit CLT Layer</h2>
            <p class="mt-1 text-sm text-gray-500">{{ $supplier->name }} → {{ $layup->name }} → Layer #{{ $layer->layer_order }}</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8">
            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <form method="POST" action="{{ route('suppliers.layups.layers.update', [$supplier, $layup, $layer]) }}" class="space-y-6">
                    @csrf
                    @method('PUT')
                    @include('layers.partials.form')

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('suppliers.show', $supplier) }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Update</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
