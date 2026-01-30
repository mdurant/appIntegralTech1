<?php

use App\Helpers\ChileanDataHelper;
use App\Models\Commune;
use App\Models\Region;
use App\Models\ServiceCategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('can update extended profile fields', function () {
    $region = Region::factory()->create();
    $commune = Commune::factory()->create(['region_id' => $region->id]);
    $category = ServiceCategory::factory()->create();
    $validRut = ChileanDataHelper::chileanRut();

    $user = User::factory()->create([
        'name' => 'Juan Pérez',
        'first_name' => 'Juan',
        'last_name' => 'Pérez',
        'gender' => 'hombre',
        'birth_date' => '1990-05-15',
        'rut' => $validRut,
        'fantasy_name' => 'Grupo Americar Spa Chile',
        'economic_activity' => 'Transporte de carga',
        'region_id' => $region->id,
        'commune_id' => $commune->id,
    ]);

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Settings\Profile::class)
        ->set('service_category_ids', [$category->id])
        ->call('updateProfileInformation')
        ->assertHasNoErrors();

    $user->refresh();
    $user->load('serviceCategories');

    expect($user->name)->toBe('Juan Pérez')
        ->and($user->first_name)->toBe('Juan')
        ->and($user->last_name)->toBe('Pérez')
        ->and($user->gender)->toBe('hombre')
        ->and($user->birth_date->format('Y-m-d'))->toBe('1990-05-15')
        ->and($user->rut)->toBe($validRut)
        ->and($user->fantasy_name)->toBe('Grupo Americar Spa Chile')
        ->and($user->economic_activity)->toBe('Transporte de carga')
        ->and($user->region_id)->toBe($region->id)
        ->and($user->commune_id)->toBe($commune->id)
        ->and($user->serviceCategories)->toHaveCount(1);
});

it('validates RUT format', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Settings\Profile::class)
        ->set('rut', 'invalid-rut')
        ->call('updateProfileInformation')
        ->assertHasErrors(['rut']);
});

it('limits service categories to 10', function () {
    $user = User::factory()->create();
    $categories = ServiceCategory::factory()->count(15)->create();

    $this->actingAs($user);

    Livewire::test(\App\Livewire\Settings\Profile::class)
        ->set('service_category_ids', $categories->pluck('id')->toArray())
        ->call('updateProfileInformation');

    $user->refresh();

    expect($user->serviceCategories)->toHaveCount(10);
});

it('resets commune when region changes', function () {
    $user = User::factory()->create();
    $region1 = Region::factory()->create();
    $commune1 = Commune::factory()->create(['region_id' => $region1->id]);
    $region2 = Region::factory()->create();

    $this->actingAs($user);

    $component = Livewire::test(\App\Livewire\Settings\Profile::class)
        ->set('region_id', $region1->id)
        ->set('commune_id', $commune1->id);

    expect($component->get('commune_id'))->toBe($commune1->id);

    $component->set('region_id', $region2->id);

    expect($component->get('commune_id'))->toBeNull();
});

it('shows fantasy name for providers', function () {
    $user = User::factory()->provider()->create([
        'fantasy_name' => 'Grupo Americar Spa Chile',
    ]);

    expect($user->shouldShowFantasyName())->toBeTrue()
        ->and($user->display_name)->toBe('Grupo Americar Spa Chile');
});

it('hides fantasy name for guests and clients', function () {
    $guest = User::factory()->guest()->create([
        'fantasy_name' => 'Test Name',
    ]);

    $client = User::factory()->client()->create([
        'fantasy_name' => 'Test Name',
    ]);

    expect($guest->shouldShowFantasyName())->toBeFalse()
        ->and($client->shouldShowFantasyName())->toBeFalse();
});
