<?php

use App\Livewire\Client\ServiceRequests\Index as ClientRequestsIndex;
use App\Models\Commune;
use App\Models\Region;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\ServiceRequestAttachment;
use App\Models\Tenant;
use App\Models\User;
use App\TenantRole;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('client puede adjuntar hasta 4 imágenes al crear solicitud', function () {
    Storage::fake('public');

    $region = Region::create(['name' => 'Metropolitana', 'code' => 'RM', 'sort_order' => 1, 'is_active' => true]);
    $commune = Commune::create(['region_id' => $region->id, 'name' => 'Santiago', 'code' => 'STG', 'sort_order' => 1, 'is_active' => true]);

    $top = ServiceCategory::create(['key' => 'construccion', 'name' => 'Construcción', 'parent_id' => null, 'sort_order' => 0]);
    $subcategory = ServiceCategory::create(['key' => 'construccion-casa', 'name' => 'Construcción Casa', 'parent_id' => $top->id, 'sort_order' => 0]);

    $client = User::factory()->client()->create();
    $tenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $tenant->users()->attach($client->id, ['role' => TenantRole::Owner->value]);
    $client->forceFill(['current_tenant_id' => $tenant->id])->save();

    $this->actingAs($client);

    Livewire::test(ClientRequestsIndex::class)
        ->set('topCategoryId', $top->id)
        ->set('subcategoryId', $subcategory->id)
        ->set('title', 'Prueba con fotos')
        ->set('description', 'Incluye 4 imágenes.')
        ->set('contact_name', 'Mauricio')
        ->set('contact_email', 'mauricio@example.com')
        ->set('contact_phone_country', '+56')
        ->set('contact_phone_number', '999999999')
        ->set('regionId', $region->id)
        ->set('communeId', $commune->id)
        ->set('address', 'Santiago')
        ->set('photos', [
            UploadedFile::fake()->image('1.jpg'),
            UploadedFile::fake()->image('2.jpg'),
            UploadedFile::fake()->image('3.jpg'),
            UploadedFile::fake()->image('4.jpg'),
        ])
        ->call('create')
        ->assertHasNoErrors();

    $request = ServiceRequest::query()->firstOrFail();
    expect($request->attachments()->count())->toBe(4);

    $request->load('attachments');

    foreach ($request->attachments as $attachment) {
        Storage::disk('public')->assertExists($attachment->path);
    }
});

test('adjuntos se sirven por ruta y respetan autorización', function () {
    Storage::fake('public');

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
        'title' => 'Solicitud con adjunto',
        'description' => 'Detalle',
        'contact_name' => 'Mauricio',
        'contact_email' => 'mauricio@example.com',
        'contact_phone' => '+56999999999',
        'location_text' => 'Santiago',
        'address' => 'Santiago',
        'status' => \App\ServiceRequestStatus::Draft->value,
    ]);

    $path = UploadedFile::fake()->image('evidencia.jpg')->storePublicly('service-requests/'.$request->id, 'public');

    $attachment = ServiceRequestAttachment::create([
        'service_request_id' => $request->id,
        'path' => $path,
        'mime' => 'image/jpeg',
        'size' => 123,
        'sort_order' => 0,
    ]);

    $this->actingAs($client);

    $this->get(route('attachments.show', $attachment))
        ->assertOk();

    $otherClient = User::factory()->client()->create();
    $otherTenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $otherTenant->users()->attach($otherClient->id, ['role' => TenantRole::Owner->value]);
    $otherClient->forceFill(['current_tenant_id' => $otherTenant->id])->save();

    $this->actingAs($otherClient);

    $this->get(route('attachments.show', $attachment))
        ->assertForbidden();
});
