<?php

use App\Livewire\Marketing\Landing;
use App\Livewire\Marketing\PublicServicesBrowse;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\Tenant;
use App\Models\User;
use App\ServiceRequestStatus;
use App\TenantRole;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('landing de marketing muestra marca y buscador', function () {
    $this->get(route('home'))
        ->assertOk()
        ->assertSee('Integral Service Tech');

    Livewire::test(Landing::class)
        ->assertSee('Tu próxima oportunidad técnica');
});

test('buscador público filtra solicitudes publicadas', function () {
    $top = ServiceCategory::create(['key' => 'servicios', 'name' => 'Servicios', 'parent_id' => null, 'sort_order' => 0]);
    $cat = ServiceCategory::create(['key' => 'electricidad', 'name' => 'Electricidad', 'parent_id' => $top->id, 'sort_order' => 0]);

    $client = User::factory()->client()->create();
    $tenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $tenant->users()->attach($client->id, ['role' => TenantRole::Owner->value]);
    $client->forceFill(['current_tenant_id' => $tenant->id])->save();

    ServiceRequest::create([
        'tenant_id' => $tenant->id,
        'category_id' => $cat->id,
        'created_by_user_id' => $client->id,
        'title' => 'Instalación eléctrica',
        'description' => 'Necesito revisión de tablero',
        'contact_name' => 'Cliente',
        'contact_email' => 'cliente@example.com',
        'contact_phone' => '+56900000000',
        'location_text' => 'Santiago',
        'address' => 'Santiago',
        'status' => ServiceRequestStatus::Published->value,
        'published_at' => now(),
        'expires_at' => now()->addDays(15),
    ]);

    Livewire::test(PublicServicesBrowse::class)
        ->set('q', 'eléctrica')
        ->assertSee('Instalación eléctrica');
});

