<x-app-layout>
    <div class="min-h-screen bg-slate-100 py-10">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white rounded-2xl shadow border border-slate-200 p-8">
                <div class="mb-8">
                    <h1 class="text-2xl font-bold text-slate-900">
                        Add Layer
                    </h1>
                    <p class="mt-1 text-sm text-slate-500">
                        Layup: {{ $layup->name }}
                    </p>
                </div>

                <form action="{{ route('layups.layers.store', $layup) }}" method="POST" class="space-y-6">
                    @csrf

                    <div>
                        <label for="layer_order" class="block text-sm font-semibold text-slate-700 mb-2">
                            Layer Order
                        </label>
                        <input
                            type="number"
                            id="layer_order"
                            name="layer_order"
                            value="{{ old('layer_order') }}"
                            class="w-full rounded-lg border-slate-300 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Example: 1"
                        >
                        @error('layer_order')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="thickness" class="block text-sm font-semibold text-slate-700 mb-2">
                            Thickness
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            id="thickness"
                            name="thickness"
                            value="{{ old('thickness') }}"
                            class="w-full rounded-lg border-slate-300 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Example: 12.50"
                        >
                        @error('thickness')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="width" class="block text-sm font-semibold text-slate-700 mb-2">
                            Width
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            id="width"
                            name="width"
                            value="{{ old('width') }}"
                            class="w-full rounded-lg border-slate-300 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Example: 120.00"
                        >
                        @error('width')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="angle" class="block text-sm font-semibold text-slate-700 mb-2">
                            Angle
                        </label>
                        <input
                            type="number"
                            step="0.01"
                            id="angle"
                            name="angle"
                            value="{{ old('angle') }}"
                            class="w-full rounded-lg border-slate-300 text-slate-900 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="Example: 90"
                        >
                        @error('angle')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                   <div style="display: flex; gap: 12px; margin-top: 30px;">
    <button
        type="submit"
        style="
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
        "
    >
        Save Layer
    </button>

    <a
        href="{{ route('layups.show', $layup) }}"
        style="
            background-color: #e5e7eb;
            color: #111827;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
        "
    >
        Cancel
    </a>
</div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>