<?php

use App\Models\Commune;
use App\Models\PaymentSimulation;
use App\Models\Region;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\Tenant;
use App\Models\User;
use App\ServiceRequestStatus;
use App\TenantRole;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('página de pago muestra monto dinámico (fee + IVA)', function () {
    $region = Region::create(['name' => 'Metropolitana', 'code' => 'RM', 'sort_order' => 1, 'is_active' => true]);
    $commune = Commune::create(['region_id' => $region->id, 'name' => 'Santiago', 'code' => 'STG', 'sort_order' => 1, 'is_active' => true]);
    $top = ServiceCategory::create(['key' => 'cat', 'name' => 'Cat', 'parent_id' => null, 'sort_order' => 0]);
    $sub = ServiceCategory::create(['key' => 'sub', 'name' => 'Sub', 'parent_id' => $top->id, 'sort_order' => 0]);

    $client = User::factory()->client()->create();
    $tenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $tenant->users()->attach($client->id, ['role' => TenantRole::Owner->value]);
    $client->forceFill(['current_tenant_id' => $tenant->id])->save();

    $request = ServiceRequest::create([
        'tenant_id' => $tenant->id,
        'category_id' => $sub->id,
        'created_by_user_id' => $client->id,
        'title' => 'Servicio para pagar',
        'description' => 'Desc',
        'region_id' => $region->id,
        'commune_id' => $commune->id,
        'status' => ServiceRequestStatus::Published->value,
        'published_at' => now(),
    ]);

    $provider = User::factory()->provider()->create();
    $this->actingAs($provider);

    $response = $this->get(route('services.payment', $request));

    $response->assertOk();
    $response->assertSee('Fee de servicio');
    $response->assertSee('Monto a pagar');
    $response->assertSee('IVA (19%)');
});

test('usuario con wallet y saldo suficiente puede pagar con wallet', function () {
    $region = Region::create(['name' => 'Metropolitana', 'code' => 'RM', 'sort_order' => 1, 'is_active' => true]);
    $commune = Commune::create(['region_id' => $region->id, 'name' => 'Santiago', 'code' => 'STG', 'sort_order' => 1, 'is_active' => true]);
    $top = ServiceCategory::create(['key' => 'cat', 'name' => 'Cat', 'parent_id' => null, 'sort_order' => 0]);
    $sub = ServiceCategory::create(['key' => 'sub', 'name' => 'Sub', 'parent_id' => $top->id, 'sort_order' => 0]);

    $client = User::factory()->client()->create();
    $tenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $tenant->users()->attach($client->id, ['role' => TenantRole::Owner->value]);
    $client->forceFill(['current_tenant_id' => $tenant->id])->save();

    $request = ServiceRequest::create([
        'tenant_id' => $tenant->id,
        'category_id' => $sub->id,
        'created_by_user_id' => $client->id,
        'title' => 'Servicio para pagar con wallet',
        'description' => 'Desc',
        'region_id' => $region->id,
        'commune_id' => $commune->id,
        'status' => ServiceRequestStatus::Published->value,
        'published_at' => now(),
    ]);

    $provider = User::factory()->provider()->create();
    $wallet = $provider->getOrCreateWallet();
    $wallet->update(['balance' => 100000]);

    $this->actingAs($provider);

    Livewire::test(\App\Livewire\Services\Payment::class, ['serviceRequest' => $request])
        ->set('payment_method', PaymentSimulation::PaymentMethodWallet)
        ->call('processPayment')
        ->assertHasNoErrors()
        ->assertRedirect(route('services.contact', $request));

    $this->assertDatabaseHas('payment_simulations', [
        'user_id' => $provider->id,
        'service_request_id' => $request->id,
        'payment_method' => PaymentSimulation::PaymentMethodWallet,
        'status' => 'approved',
    ]);

    $payment = PaymentSimulation::where('user_id', $provider->id)->where('service_request_id', $request->id)->first();
    expect($payment->wallet_transaction_id)->not->toBeNull();
});
