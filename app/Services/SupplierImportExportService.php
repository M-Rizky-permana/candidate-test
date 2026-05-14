<?php

namespace App\Services;

use App\Exceptions\ImportConflictException;
use App\Models\CltLayup;
use App\Models\Supplier;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SupplierImportExportService
{
    public function exportSupplier(Supplier $supplier): array
    {
        $supplier->load([
            'layups' => fn ($query) => $query->with(['layers' => fn ($layerQuery) => $layerQuery->orderBy('layer_order')])->orderBy('name'),
        ]);

        return [
            'supplier' => [
                'id' => $supplier->id,
                'name' => $supplier->name,
            ],
            'layups' => $supplier->layups->map(fn ($layup) => [
                'id' => $layup->id,
                'name' => $layup->name,
                'layers' => $layup->layers->map(fn ($layer) => [
                    'id' => $layer->id,
                    'layer_order' => $layer->layer_order,
                    'thickness' => (float) $layer->thickness,
                    'width' => (float) $layer->width,
                    'angle' => (float) $layer->angle,
                ])->values()->all(),
            ])->values()->all(),
        ];
    }

    /**
     * @throws ImportConflictException
     * @throws ValidationException
     */
    public function importToSupplier(Supplier $supplier, array $payload, string $strategy): array
    {
        $layups = $this->extractLayups($payload);
        $this->validateLayups($layups);

        $conflicts = $this->detectConflicts($supplier, $layups);

        if ($strategy === 'reject' && count($conflicts) > 0) {
            throw new ImportConflictException($conflicts);
        }

        return DB::transaction(function () use ($supplier, $layups, $strategy, $conflicts) {
            $summary = [
                'created_layups' => 0,
                'duplicated_layups' => 0,
                'created_layers' => 0,
                'updated_layers' => 0,
                'skipped_conflicts' => 0,
                'conflicts_count' => count($conflicts),
            ];

            $conflictLayupNames = collect($conflicts)->pluck('layup_name')->unique()->values()->all();

            foreach ($layups as $incomingLayup) {
                $incomingLayers = Arr::get($incomingLayup, 'layers', []);
                $hasLayupConflict = in_array($incomingLayup['name'], $conflictLayupNames, true);
                $existingLayup = $supplier->layups()->with('layers')->where('name', $incomingLayup['name'])->first();

                if ($strategy === 'duplicate' && $hasLayupConflict) {
                    $targetLayup = $supplier->layups()->create([
                        'name' => $this->uniqueImportedLayupName($supplier, $incomingLayup['name']),
                    ]);
                    $summary['duplicated_layups']++;

                    foreach ($incomingLayers as $incomingLayer) {
                        $targetLayup->layers()->create($this->cleanLayerPayload($incomingLayer));
                        $summary['created_layers']++;
                    }

                    continue;
                }

                if (! $existingLayup) {
                    $targetLayup = $supplier->layups()->create(['name' => $incomingLayup['name']]);
                    $summary['created_layups']++;
                } else {
                    $targetLayup = $existingLayup;
                }

                foreach ($incomingLayers as $incomingLayer) {
                    $layerPayload = $this->cleanLayerPayload($incomingLayer);
                    $existingLayer = $targetLayup->layers()->where('layer_order', $layerPayload['layer_order'])->first();

                    if (! $existingLayer) {
                        $targetLayup->layers()->create($layerPayload);
                        $summary['created_layers']++;
                        continue;
                    }

                    $isConflict = $this->layerIsDifferent($existingLayer, $layerPayload);

                    if (! $isConflict) {
                        continue;
                    }

                    if ($strategy === 'skip') {
                        $summary['skipped_conflicts']++;
                        continue;
                    }

                    $existingLayer->update($layerPayload);
                    $summary['updated_layers']++;
                }
            }

            return $summary;
        });
    }

    private function extractLayups(array $payload): array
    {
        $layups = Arr::get($payload, 'layups', Arr::get($payload, 'supplier.layups', []));

        if (! is_array($layups)) {
            throw ValidationException::withMessages([
                'file' => 'Invalid JSON format: layups must be an array.',
            ]);
        }

        return $layups;
    }

    private function validateLayups(array $layups): void
    {
        Validator::make(['layups' => $layups], [
            'layups' => ['required', 'array'],
            'layups.*.name' => ['required', 'string', 'max:255'],
            'layups.*.layers' => ['nullable', 'array'],
            'layups.*.layers.*.layer_order' => ['required', 'integer', 'min:1'],
            'layups.*.layers.*.thickness' => ['required', 'numeric', 'min:0'],
            'layups.*.layers.*.width' => ['required', 'numeric', 'min:0'],
            'layups.*.layers.*.angle' => ['required', 'numeric', 'between:-360,360'],
        ])->validate();
    }

    private function detectConflicts(Supplier $supplier, array $layups): array
    {
        $supplier->loadMissing(['layups.layers']);
        $conflicts = [];

        foreach ($layups as $incomingLayup) {
            $existingLayup = $supplier->layups->firstWhere('name', $incomingLayup['name']);

            if (! $existingLayup) {
                continue;
            }

            foreach (Arr::get($incomingLayup, 'layers', []) as $incomingLayer) {
                $layerPayload = $this->cleanLayerPayload($incomingLayer);
                $existingLayer = $existingLayup->layers->firstWhere('layer_order', $layerPayload['layer_order']);

                if (! $existingLayer || ! $this->layerIsDifferent($existingLayer, $layerPayload)) {
                    continue;
                }

                $conflicts[] = [
                    'layup_id' => $existingLayup->id,
                    'layup_name' => $existingLayup->name,
                    'layer_order' => $layerPayload['layer_order'],
                    'existing' => [
                        'thickness' => (float) $existingLayer->thickness,
                        'width' => (float) $existingLayer->width,
                        'angle' => (float) $existingLayer->angle,
                    ],
                    'incoming' => [
                        'thickness' => (float) $layerPayload['thickness'],
                        'width' => (float) $layerPayload['width'],
                        'angle' => (float) $layerPayload['angle'],
                    ],
                    'differences' => $this->differentFields($existingLayer, $layerPayload),
                ];
            }
        }

        return $conflicts;
    }

    private function cleanLayerPayload(array $layer): array
    {
        return [
            'layer_order' => (int) $layer['layer_order'],
            'thickness' => round((float) $layer['thickness'], 2),
            'width' => round((float) $layer['width'], 2),
            'angle' => round((float) $layer['angle'], 2),
        ];
    }

    private function layerIsDifferent(object $existingLayer, array $incomingLayer): bool
    {
        return count($this->differentFields($existingLayer, $incomingLayer)) > 0;
    }

    private function differentFields(object $existingLayer, array $incomingLayer): array
    {
        $fields = [];

        foreach (['thickness', 'width', 'angle'] as $field) {
            if (abs((float) $existingLayer->{$field} - (float) $incomingLayer[$field]) > 0.00001) {
                $fields[] = $field;
            }
        }

        return $fields;
    }

    private function uniqueImportedLayupName(Supplier $supplier, string $name): string
    {
        $baseName = $name . ' (imported)';
        $candidate = $baseName;
        $index = 2;

        while ($supplier->layups()->where('name', $candidate)->exists()) {
            $candidate = $baseName . ' ' . $index;
            $index++;
        }

        return $candidate;
    }
}
