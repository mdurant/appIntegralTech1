<?php

use App\Livewire\Client\ServiceRequests\Edit as ClientRequestsEdit;
use App\Models\Commune;
use App\Models\Region;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\Tenant;
use App\Models\User;
use App\ServiceRequestStatus;
use App\TenantRole;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('cliente puede acceder a editar solicitud en draft', function () {
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
        'title' => 'Trabajo draft',
        'description' => 'Desc',
        'contact_name' => 'Juan',
        'contact_email' => 'juan@test.com',
        'contact_phone' => '56912345678',
        'location_text' => 'Santiago, Metropolitana',
        'address' => 'Calle 1',
        'region_id' => $region->id,
        'commune_id' => $commune->id,
        'status' => ServiceRequestStatus::Draft,
    ]);

    $this->actingAs($client);

    $this->get(route('client.requests.edit', $request))
        ->assertOk()
        ->assertSee('Editar solicitud')
        ->assertSee('Trabajo draft');

    Livewire::test(ClientRequestsEdit::class, ['serviceRequest' => $request])
        ->set('title', 'Trabajo actualizado')
        ->set('description', 'Nueva desc')
        ->set('contact_name', 'Juan')
        ->set('contact_email', 'juan@test.com')
        ->set('contact_phone_country', '+56')
        ->set('contact_phone_number', '912345678')
        ->set('regionId', $region->id)
        ->set('communeId', $commune->id)
        ->set('address', 'Calle 1')
        ->call('save')
        ->assertHasNoErrors()
        ->assertRedirect(route('client.requests.index'));

    $request->refresh();
    expect($request->title)->toBe('Trabajo actualizado');
});

test('cliente no puede editar solicitud que no es draft', function () {
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
        'title' => 'Publicada',
        'description' => 'Desc',
        'contact_name' => 'Juan',
        'contact_email' => 'juan@test.com',
        'contact_phone' => '123',
        'location_text' => 'Santiago',
        'address' => 'Calle 1',
        'status' => ServiceRequestStatus::Published,
    ]);

    $this->actingAs($client);

    $this->get(route('client.requests.edit', $request))
        ->assertForbidden();
});

test('ruta PDF genera y devuelve PDF cuando la solicitud no tiene pdf_path', function () {
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
        'title' => 'Sin PDF',
        'description' => 'Desc',
        'contact_name' => 'Juan',
        'contact_email' => 'juan@test.com',
        'contact_phone' => '123',
        'location_text' => 'Santiago',
        'address' => 'Calle 1',
        'status' => ServiceRequestStatus::Draft,
        'pdf_path' => null,
    ]);

    $this->actingAs($client);

    $response = $this->get(route('client.requests.pdf', $request));

    $response->assertSuccessful();
    $response->assertHeader('content-type', 'application/pdf');
});

test('cliente ajeno no puede acceder al PDF de otra solicitud', function () {
    $top = ServiceCategory::create(['key' => 'cat', 'name' => 'Cat', 'parent_id' => null, 'sort_order' => 0]);
    $sub = ServiceCategory::create(['key' => 'sub', 'name' => 'Sub', 'parent_id' => $top->id, 'sort_order' => 0]);

    $owner = User::factory()->client()->create();
    $other = User::factory()->client()->create();
    $tenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $tenant->users()->attach($owner->id, ['role' => TenantRole::Owner->value]);
    $owner->forceFill(['current_tenant_id' => $tenant->id])->save();

    $otherTenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $otherTenant->users()->attach($other->id, ['role' => TenantRole::Owner->value]);
    $other->forceFill(['current_tenant_id' => $otherTenant->id])->save();

    $request = ServiceRequest::create([
        'tenant_id' => $tenant->id,
        'category_id' => $sub->id,
        'created_by_user_id' => $owner->id,
        'title' => 'Solicitud del otro',
        'description' => 'Desc',
        'contact_name' => 'Juan',
        'contact_email' => 'juan@test.com',
        'contact_phone' => '123',
        'location_text' => 'Santiago',
        'address' => 'Calle 1',
        'status' => ServiceRequestStatus::Draft,
        'pdf_path' => 'service-requests/999/fake.pdf',
    ]);

    $this->actingAs($other);

    $this->get(route('client.requests.pdf', $request))
        ->assertForbidden();
});
