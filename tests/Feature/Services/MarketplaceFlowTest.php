<?php

use App\Livewire\Client\ServiceRequests\Index as ClientRequestsIndex;
use App\Livewire\Services\Show as ServicesShow;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\Tenant;
use App\Models\User;
use App\ServiceRequestStatus;
use App\TenantRole;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('client puede crear y publicar solicitud; user puede ofertar', function () {
    $top = ServiceCategory::create(['key' => 'construccion', 'name' => 'Construcción', 'parent_id' => null, 'sort_order' => 0]);
    $subcategory = ServiceCategory::create(['key' => 'construccion-casa', 'name' => 'Construcción Casa', 'parent_id' => $top->id, 'sort_order' => 0]);

    $client = User::factory()->client()->create();
    $clientTenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $clientTenant->users()->attach($client->id, ['role' => TenantRole::Owner->value]);
    $client->forceFill(['current_tenant_id' => $clientTenant->id])->save();

    $provider = User::factory()->provider()->create();

    $this->actingAs($client);

    Livewire::test(ClientRequestsIndex::class)
        ->set('topCategoryId', $top->id)
        ->set('subcategoryId', $subcategory->id)
        ->set('title', 'Necesito instalar aire acondicionado')
        ->set('description', 'Instalación completa en oficina pequeña.')
        ->set('contact_name', 'Juan Perez')
        ->set('contact_email', 'juan@example.com')
        ->set('contact_phone', '+56911112222')
        ->set('location_text', 'La Cisterna, Santiago')
        ->set('address', 'Av. Siempre Viva 123')
        ->call('create')
        ->assertHasNoErrors();

    $request = ServiceRequest::query()->firstOrFail();
    expect($request->status->value)->toBe(ServiceRequestStatus::Draft->value);

    Livewire::test(ClientRequestsIndex::class)
        ->call('publish', $request->id)
        ->assertHasNoErrors();

    $request->refresh();
    expect($request->status->value)->toBe(ServiceRequestStatus::Published->value);

    $this->actingAs($provider);

    $this->get(route('services.browse'))
        ->assertOk()
        ->assertSee('Necesito instalar aire acondicionado');

    Livewire::test(ServicesShow::class, ['serviceRequest' => $request])
        ->set('amount', '150000')
        ->set('message', 'Puedo hacerlo esta semana.')
        ->call('submit')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('service_bids', [
        'service_request_id' => $request->id,
        'user_id' => $provider->id,
    ]);

    $bid = \App\Models\ServiceBid::query()
        ->where('service_request_id', $request->id)
        ->where('user_id', $provider->id)
        ->firstOrFail();

    expect($bid->valid_until)->not->toBeNull();
});

test('guest no puede ofertar', function () {
    $top = ServiceCategory::create(['key' => 'construccion', 'name' => 'Construcción', 'parent_id' => null, 'sort_order' => 0]);
    $subcategory = ServiceCategory::create(['key' => 'construccion-casa', 'name' => 'Construcción Casa', 'parent_id' => $top->id, 'sort_order' => 0]);

    $client = User::factory()->client()->create();
    $tenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $tenant->users()->attach($client->id, ['role' => TenantRole::Owner->value]);
    $client->forceFill(['current_tenant_id' => $tenant->id])->save();

    $request = ServiceRequest::create([
        'tenant_id' => $tenant->id,
        'category_id' => $subcategory->id,
        'created_by_user_id' => $client->id,
        'title' => 'Reparación eléctrica',
        'description' => 'Revisión de tablero y enchufes.',
        'status' => ServiceRequestStatus::Published->value,
        'published_at' => now(),
    ]);

    $guest = User::factory()->guest()->create();

    $this->actingAs($guest);

    Livewire::test(ServicesShow::class, ['serviceRequest' => $request])
        ->set('amount', '10000')
        ->call('submit')
        ->assertStatus(403);
});

test('client de otro tenant no puede ver draft ajeno', function () {
    $top = ServiceCategory::create(['key' => 'construccion', 'name' => 'Construcción', 'parent_id' => null, 'sort_order' => 0]);
    $subcategory = ServiceCategory::create(['key' => 'construccion-casa', 'name' => 'Construcción Casa', 'parent_id' => $top->id, 'sort_order' => 0]);

    $clientA = User::factory()->client()->create();
    $tenantA = Tenant::factory()->create(['created_by_user_id' => null]);
    $tenantA->users()->attach($clientA->id, ['role' => TenantRole::Owner->value]);
    $clientA->forceFill(['current_tenant_id' => $tenantA->id])->save();

    $draft = ServiceRequest::create([
        'tenant_id' => $tenantA->id,
        'category_id' => $subcategory->id,
        'created_by_user_id' => $clientA->id,
        'title' => 'Draft privado',
        'description' => 'No debería ser visible.',
        'status' => ServiceRequestStatus::Draft->value,
    ]);

    $clientB = User::factory()->client()->create();
    $tenantB = Tenant::factory()->create(['created_by_user_id' => null]);
    $tenantB->users()->attach($clientB->id, ['role' => TenantRole::Owner->value]);
    $clientB->forceFill(['current_tenant_id' => $tenantB->id])->save();

    $this->actingAs($clientB);

    Livewire::test(ServicesShow::class, ['serviceRequest' => $draft])
        ->assertForbidden();
});
