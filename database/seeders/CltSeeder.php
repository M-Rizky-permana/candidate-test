<?php

namespace Database\Seeders;

use App\Models\Supplier;
use Illuminate\Database\Seeder;

class CltSeeder extends Seeder
{
    public function run(): void
    {
        $supplier = Supplier::firstOrCreate(['name' => 'Nordic Timber']);

        $layup = $supplier->layups()->firstOrCreate(['name' => 'Standard 3-Ply']);

        $layers = [
            ['layer_order' => 1, 'thickness' => 20, 'width' => 1200, 'angle' => 0],
            ['layer_order' => 2, 'thickness' => 20, 'width' => 1200, 'angle' => 90],
            ['layer_order' => 3, 'thickness' => 20, 'width' => 1200, 'angle' => 0],
        ];

        foreach ($layers as $layer) {
            $layup->layers()->updateOrCreate(
                ['layer_order' => $layer['layer_order']],
                $layer
            );
        }
    }
}
