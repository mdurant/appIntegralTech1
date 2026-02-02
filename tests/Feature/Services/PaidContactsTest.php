<?php

use App\Models\Commune;
use App\Models\PaymentSimulation;
use App\Models\Region;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\Tenant;
use App\Models\User;
use App\ServiceRequestStatus;
use App\SystemRole;
use App\TenantRole;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('proveedor puede acceder a mis contactos comprados', function () {
    $provider = User::factory()->create(['system_role' => SystemRole::User->value]);
    $this->actingAs($provider);

    $this->get(route('services.paid-contacts'))
        ->assertOk()
        ->assertSee('Contactos comprados');
});

test('mis contactos comprados muestra solo servicios con pago aprobado', function () {
    $region = Region::create(['name' => 'Metropolitana', 'code' => 'RM', 'sort_order' => 1, 'is_active' => true]);
    $commune = Commune::create(['region_id' => $region->id, 'name' => 'Santiago', 'code' => 'STG', 'sort_order' => 1, 'is_active' => true]);
    $cat = ServiceCategory::create(['key' => 'cat', 'name' => 'Cat', 'parent_id' => null, 'sort_order' => 0]);

    $client = User::factory()->client()->create();
    $tenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $tenant->users()->attach($client->id, ['role' => TenantRole::Owner->value]);
    $client->forceFill(['current_tenant_id' => $tenant->id])->save();

    $paidRequest = ServiceRequest::create([
        'tenant_id' => $tenant->id,
        'category_id' => $cat->id,
        'created_by_user_id' => $client->id,
        'title' => 'Servicio ya pagado',
        'description' => 'Desc',
        'contact_name' => 'Juan Pérez',
        'contact_email' => 'juan@test.com',
        'contact_phone' => '+56912345678',
        'region_id' => $region->id,
        'commune_id' => $commune->id,
        'status' => ServiceRequestStatus::Published->value,
        'published_at' => now(),
    ]);

    $otherRequest = ServiceRequest::create([
        'tenant_id' => $tenant->id,
        'category_id' => $cat->id,
        'created_by_user_id' => $client->id,
        'title' => 'Otro servicio sin pago',
        'description' => 'Desc',
        'region_id' => $region->id,
        'commune_id' => $commune->id,
        'status' => ServiceRequestStatus::Published->value,
        'published_at' => now(),
    ]);

    $provider = User::factory()->create(['system_role' => SystemRole::User->value]);
    PaymentSimulation::create([
        'user_id' => $provider->id,
        'service_request_id' => $paidRequest->id,
        'amount' => 5000,
        'card_last_four' => '4242',
        'cardholder_name' => 'Test User',
        'status' => 'approved',
        'paid_at' => now(),
    ]);

    $this->actingAs($provider);

    $response = $this->get(route('services.paid-contacts'));
    $response->assertOk();
    $response->assertSee('Servicio ya pagado');
    $response->assertSee('Juan Pérez');
    $response->assertSee('juan@test.com');
    $response->assertDontSee('Otro servicio sin pago');
});

test('cliente no puede acceder a mis contactos comprados', function () {
    $client = User::factory()->client()->create();
    $this->actingAs($client);

    $this->get(route('services.paid-contacts'))
        ->assertRedirect(route('dashboard'));
});

test('mis contactos comprados muestra mensaje vacío cuando no hay compras', function () {
    $provider = User::factory()->create(['system_role' => SystemRole::User->value]);
    $this->actingAs($provider);

    $response = $this->get(route('services.paid-contacts'));
    $response->assertOk();
    $response->assertSee('Aún no has comprado el contacto de ningún servicio');
    $response->assertSee('Servicios');
});

test('flujo completo con pago con tarjeta: pagar contacto → aparece en Contactos comprados', function () {
    $region = Region::create(['name' => 'Metropolitana', 'code' => 'RM', 'sort_order' => 1, 'is_active' => true]);
    $commune = Commune::create(['region_id' => $region->id, 'name' => 'Santiago', 'code' => 'STG', 'sort_order' => 1, 'is_active' => true]);
    $cat = ServiceCategory::create(['key' => 'cat', 'name' => 'Categoría', 'parent_id' => null, 'sort_order' => 0]);

    $client = User::factory()->client()->create();
    $tenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $tenant->users()->attach($client->id, ['role' => TenantRole::Owner->value]);
    $client->forceFill(['current_tenant_id' => $tenant->id])->save();

    $serviceRequest = ServiceRequest::create([
        'tenant_id' => $tenant->id,
        'category_id' => $cat->id,
        'created_by_user_id' => $client->id,
        'title' => 'Servicio pagado con tarjeta',
        'description' => 'Descripción',
        'contact_name' => 'Carlos Rojas',
        'contact_email' => 'carlos@ejemplo.cl',
        'contact_phone' => '+56911223344',
        'region_id' => $region->id,
        'commune_id' => $commune->id,
        'status' => ServiceRequestStatus::Published->value,
        'published_at' => now(),
    ]);

    $provider = User::factory()->create(['system_role' => SystemRole::User->value]);
    $this->actingAs($provider);

    Livewire::test(\App\Livewire\Services\Payment::class, ['serviceRequest' => $serviceRequest])
        ->set('cardholder_name', 'Proveedor Demo')
        ->set('card_number', '4111111111111111')
        ->set('expiry_month', '12')
        ->set('expiry_year', (string) (now()->year + 2))
        ->set('cvv', '123')
        ->set('email', $provider->email)
        ->call('processPayment')
        ->assertHasNoErrors()
        ->assertRedirect(route('services.contact', $serviceRequest));

    $response = $this->get(route('services.paid-contacts'));
    $response->assertOk();
    $response->assertSee('Servicio pagado con tarjeta');
    $response->assertSee('Carlos Rojas');
    $response->assertSee('carlos@ejemplo.cl');
});
