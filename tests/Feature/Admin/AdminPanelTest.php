<?php

use App\Models\User;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('non-admins cannot access admin panel', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get('/admin')
        ->assertForbidden();

    $this->get('/admin/users')->assertForbidden();
    $this->get('/admin/tenants')->assertForbidden();
    $this->get('/admin/service-requests')->assertForbidden();
    $this->get('/admin/service-bids')->assertForbidden();
});

test('admin can access admin panel pages', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get('/admin')
        ->assertOk()
        ->assertSee('Admin');

    $this->get('/admin/users')->assertOk();
    $this->get('/admin/tenants')->assertOk();
    $this->get('/admin/service-requests')->assertOk();
    $this->get('/admin/service-bids')->assertOk();
});

