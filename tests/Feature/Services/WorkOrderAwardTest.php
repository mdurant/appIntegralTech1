<?php

use App\Livewire\Client\ServiceRequests\Show as ClientRequestShow;
use App\Models\ServiceBid;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\Tenant;
use App\Models\User;
use App\ServiceRequestStatus;
use App\TenantRole;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('client puede adjudicar una cotización y se crea OT', function () {
    $top = ServiceCategory::create(['key' => 'construccion', 'name' => 'Construcción', 'parent_id' => null, 'sort_order' => 0]);
    $subcategory = ServiceCategory::create(['key' => 'construccion-casa', 'name' => 'Construcción Casa', 'parent_id' => $top->id, 'sort_order' => 0]);

    $client = User::factory()->client()->create();
    $tenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $tenant->users()->attach($client->id, ['role' => TenantRole::Owner->value]);
    $client->forceFill(['current_tenant_id' => $tenant->id])->save();

    $provider = User::factory()->provider()->create();

    $request = ServiceRequest::create([
        'tenant_id' => $tenant->id,
        'category_id' => $subcategory->id,
        'created_by_user_id' => $client->id,
        'title' => 'Construir segundo piso',
        'description' => 'Detalle',
        'notes' => 'Necesito hacer un 2do piso...',
        'contact_name' => 'Juan Perez',
        'contact_email' => 'juan@example.com',
        'contact_phone' => '+56911112222',
        'location_text' => 'La Cisterna, Santiago',
        'address' => 'Av. Siempre Viva 123',
        'status' => ServiceRequestStatus::Published->value,
        'published_at' => now(),
        'expires_at' => now()->addDays(15),
    ]);

    $bid = ServiceBid::create([
        'service_request_id' => $request->id,
        'user_id' => $provider->id,
        'amount' => 150000,
        'currency' => 'CLP',
        'message' => 'Puedo hacerlo esta semana.',
        'status' => 'submitted',
        'valid_until' => now()->addDays(15),
    ]);

    $this->actingAs($client);

    Livewire::test(ClientRequestShow::class, ['serviceRequest' => $request])
        ->call('award', $bid->id)
        ->assertOk();

    expect($request->refresh()->status->value)->toBe(ServiceRequestStatus::Awarded->value);

    $this->assertDatabaseHas('work_orders', [
        'service_request_id' => $request->id,
        'service_bid_id' => $bid->id,
        'tenant_id' => $tenant->id,
        'awarded_to_user_id' => $provider->id,
    ]);
});
