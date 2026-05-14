<x-app-layout>
    <x-slot name="header">
        <div>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">Import Supplier Data</h2>
            <p class="mt-1 text-sm text-gray-500">Target supplier: {{ $supplier->name }}</p>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-5xl space-y-6 px-4 sm:px-6 lg:px-8">
            @include('suppliers.partials.flash')

            <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-gray-200">
                <form method="POST" action="{{ route('suppliers.import', $supplier) }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf

                    <div>
                        <x-input-label for="file" value="JSON File" />
                        <input id="file" name="file" type="file" accept=".json,application/json" class="mt-1 block w-full rounded-lg border border-gray-300 text-sm file:mr-4 file:border-0 file:bg-indigo-50 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-indigo-700 hover:file:bg-indigo-100" required>
                        <x-input-error class="mt-2" :messages="$errors->get('file')" />
                        <p class="mt-2 text-xs text-gray-500">Accepted format: export JSON from this app, containing layups and layers.</p>
                    </div>

                    <div>
                        <x-input-label for="strategy" value="Conflict Strategy" />
                        <select id="strategy" name="strategy" class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="reject" @selected(old('strategy', 'reject') === 'reject')>Reject Entire Import - safest, returns conflict report</option>
                            <option value="overwrite" @selected(old('strategy') === 'overwrite')>Overwrite Existing - incoming layer replaces current layer</option>
                            <option value="skip" @selected(old('strategy') === 'skip')>Skip Conflict - keep current layer</option>
                            <option value="duplicate" @selected(old('strategy') === 'duplicate')>Duplicate Layup - create "name (imported)" when conflict exists</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('strategy')" />
                    </div>

                    <div class="rounded-xl bg-gray-50 p-4 text-sm text-gray-600">
                        <div class="font-semibold text-gray-800">Expected JSON</div>
<pre class="mt-3 overflow-x-auto rounded-lg bg-gray-900 p-4 text-xs text-gray-100">{
  "supplier": { "name": "Supplier A" },
  "layups": [
    {
      "name": "Layup 1",
      "layers": [
        { "layer_order": 1, "thickness": 10, "width": 120, "angle": 90 }
      ]
    }
  ]
}</pre>
                    </div>

                    <div class="flex justify-end gap-3">
                        <a href="{{ route('suppliers.show', $supplier) }}" class="rounded-lg border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50">Cancel</a>
                        <button type="submit" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700">Run Import</button>
                    </div>
                </form>
            </div>

            @if(session('conflict_report'))
                <div class="rounded-2xl border border-rose-200 bg-white shadow-sm">
                    <div class="border-b border-rose-100 bg-rose-50 px-6 py-5">
                        <h3 class="text-lg font-semibold text-rose-900">Conflict Report</h3>
                        <p class="mt-1 text-sm text-rose-700">Existing and incoming values are shown side-by-side. Re-run import using overwrite, skip, or duplicate strategy to resolve.</p>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @foreach(session('conflict_report') as $index => $conflict)
                            <div class="p-6">
                                <div class="mb-4 flex flex-wrap items-center justify-between gap-2">
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">Conflict {{ $index + 1 }} of {{ count(session('conflict_report')) }}</div>
                                        <div class="text-sm text-gray-500">Layup: {{ $conflict['layup_name'] }} · Layer order: #{{ $conflict['layer_order'] }}</div>
                                    </div>
                                    <div class="rounded-full bg-rose-100 px-3 py-1 text-xs font-semibold text-rose-700">
                                        Different: {{ implode(', ', $conflict['differences']) }}
                                    </div>
                                </div>

                                <div class="grid gap-4 md:grid-cols-2">
                                    <div class="rounded-xl border border-gray-200 p-4">
                                        <h4 class="mb-3 font-semibold text-gray-900">Existing Version</h4>
                                        @foreach($conflict['existing'] as $field => $value)
                                            <div class="flex justify-between border-t border-gray-100 py-2 text-sm {{ in_array($field, $conflict['differences']) ? 'bg-rose-50 px-2' : '' }}">
                                                <span class="text-gray-500">{{ ucfirst($field) }}</span>
                                                <span class="font-medium text-gray-900">{{ $value }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="rounded-xl border border-indigo-200 p-4">
                                        <h4 class="mb-3 font-semibold text-indigo-900">Incoming Version</h4>
                                        @foreach($conflict['incoming'] as $field => $value)
                                            <div class="flex justify-between border-t border-gray-100 py-2 text-sm {{ in_array($field, $conflict['differences']) ? 'bg-indigo-50 px-2' : '' }}">
                                                <span class="text-gray-500">{{ ucfirst($field) }}</span>
                                                <span class="font-medium text-gray-900">{{ $value }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
