<?php

use App\Models\Region;
use App\Models\ServiceCategory;
use App\Models\ServiceRequest;
use App\Models\Tenant;
use App\Models\User;
use App\ServiceRequestStatus;
use App\Services\ContactFeeCalculator;
use App\TenantRole;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

beforeEach(function () {
    $this->top = ServiceCategory::create(['key' => 'cat', 'name' => 'Cat', 'parent_id' => null, 'sort_order' => 0]);
    $this->sub = ServiceCategory::create(['key' => 'sub', 'name' => 'Sub', 'parent_id' => $this->top->id, 'sort_order' => 0]);
    $this->client = User::factory()->client()->create();
    $this->tenant = Tenant::factory()->create(['created_by_user_id' => null]);
    $this->tenant->users()->attach($this->client->id, ['role' => TenantRole::Owner->value]);
    $this->client->forceFill(['current_tenant_id' => $this->tenant->id])->save();
});

test('calcula fee con base y IVA cuando no hay coeficientes', function () {
    $request = ServiceRequest::create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->sub->id,
        'created_by_user_id' => $this->client->id,
        'title' => 'Servicio',
        'description' => 'Desc',
        'status' => ServiceRequestStatus::Published->value,
        'published_at' => now(),
    ]);

    $result = ContactFeeCalculator::calculate($request);

    expect($result)->toHaveKeys(['fee_net', 'iva', 'total', 'breakdown']);
    expect($result['fee_net'])->toBeInt();
    expect($result['iva'])->toBeInt();
    expect($result['total'])->toBe($result['fee_net'] + $result['iva']);
    $baseFee = (int) config('contact_fee.base_fee', 2500);
    expect($result['fee_net'])->toBe($baseFee);
    expect($result['total'])->toBeGreaterThan($result['fee_net']);
});

test('incluye coeficiente de categoría cuando fee_multiplier está definido', function () {
    $this->sub->update(['fee_multiplier' => 0.2]);
    $request = ServiceRequest::create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->sub->id,
        'created_by_user_id' => $this->client->id,
        'title' => 'Servicio',
        'description' => 'Desc',
        'status' => ServiceRequestStatus::Published->value,
        'published_at' => now(),
    ]);

    $result = ContactFeeCalculator::calculate($request);

    $baseFee = (int) config('contact_fee.base_fee', 2500);
    expect($result['fee_net'])->toBeGreaterThan($baseFee);
    expect($result['breakdown']['coef_category'])->toBe(0.2);
});

test('incluye coeficiente de ubicación en región premium', function () {
    $region = Region::create(['name' => 'Metropolitana', 'code' => 'RM', 'sort_order' => 1, 'is_active' => true]);
    $request = ServiceRequest::create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->sub->id,
        'created_by_user_id' => $this->client->id,
        'title' => 'Servicio',
        'description' => 'Desc',
        'region_id' => $region->id,
        'status' => ServiceRequestStatus::Published->value,
        'published_at' => now(),
    ]);

    $result = ContactFeeCalculator::calculate($request);

    expect($result['breakdown']['coef_location'])->toBeGreaterThan(0);
    expect($result['fee_net'])->toBeGreaterThan((int) config('contact_fee.base_fee', 2500));
});

test('breakdown incluye base_fee e iva_rate', function () {
    $request = ServiceRequest::create([
        'tenant_id' => $this->tenant->id,
        'category_id' => $this->sub->id,
        'created_by_user_id' => $this->client->id,
        'title' => 'Servicio',
        'description' => 'Desc',
        'status' => ServiceRequestStatus::Published->value,
        'published_at' => now(),
    ]);

    $result = ContactFeeCalculator::calculate($request);

    expect($result['breakdown'])->toHaveKeys(['base_fee', 'iva_rate', 'coef_category', 'coef_demand', 'coef_location', 'coef_age_discount']);
});
