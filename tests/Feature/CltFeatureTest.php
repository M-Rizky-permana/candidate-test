<?php

namespace Tests\Feature;

use App\Models\CltLayer;
use App\Models\CltLayup;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class CltFeatureTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_create_supplier(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('suppliers.store'), [
            'name' => 'Supplier A',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('suppliers', ['name' => 'Supplier A']);
    }

    public function test_export_contains_supplier_layups_and_layers(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::create(['name' => 'Supplier A']);
        $layup = $supplier->layups()->create(['name' => 'Layup A']);
        $layup->layers()->create([
            'layer_order' => 1,
            'thickness' => 10,
            'width' => 120,
            'angle' => 90,
        ]);

        $response = $this->actingAs($user)->get(route('suppliers.export', $supplier));

        $response->assertOk();
        $response->assertHeader('content-type', 'application/json');
        $response->assertSee('Supplier A');
        $response->assertSee('Layup A');
        $response->assertSee('layer_order');
    }

    public function test_import_reject_strategy_returns_conflict_report(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::create(['name' => 'Supplier A']);
        $layup = $supplier->layups()->create(['name' => 'Layup A']);
        $layup->layers()->create([
            'layer_order' => 1,
            'thickness' => 10,
            'width' => 120,
            'angle' => 90,
        ]);

        $payload = [
            'layups' => [[
                'name' => 'Layup A',
                'layers' => [[
                    'layer_order' => 1,
                    'thickness' => 15,
                    'width' => 120,
                    'angle' => 45,
                ]],
            ]],
        ];

        $file = UploadedFile::fake()->createWithContent('supplier.json', json_encode($payload));

        $response = $this->actingAs($user)->post(route('suppliers.import', $supplier), [
            'strategy' => 'reject',
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('conflict_report');
        $this->assertEquals(10.0, (float) $layup->layers()->first()->thickness);
    }

    public function test_import_overwrite_strategy_updates_conflicting_layer(): void
    {
        $user = User::factory()->create();
        $supplier = Supplier::create(['name' => 'Supplier A']);
        $layup = $supplier->layups()->create(['name' => 'Layup A']);
        $layer = $layup->layers()->create([
            'layer_order' => 1,
            'thickness' => 10,
            'width' => 120,
            'angle' => 90,
        ]);

        $payload = [
            'layups' => [[
                'name' => 'Layup A',
                'layers' => [[
                    'layer_order' => 1,
                    'thickness' => 15,
                    'width' => 130,
                    'angle' => 45,
                ]],
            ]],
        ];

        $file = UploadedFile::fake()->createWithContent('supplier.json', json_encode($payload));

        $response = $this->actingAs($user)->post(route('suppliers.import', $supplier), [
            'strategy' => 'overwrite',
            'file' => $file,
        ]);

        $response->assertRedirect(route('suppliers.show', $supplier));
        $this->assertEquals(15.0, (float) $layer->fresh()->thickness);
        $this->assertEquals(130.0, (float) $layer->fresh()->width);
        $this->assertEquals(45.0, (float) $layer->fresh()->angle);
    }
}
