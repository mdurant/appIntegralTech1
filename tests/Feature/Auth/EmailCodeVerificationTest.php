<?php

use App\Livewire\Auth\VerifyCode;
use App\Models\EmailVerificationCode;
use App\Models\User;
use App\Services\EmailVerificationCodeService;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('usuario no verificado es redirigido a verify-code al visitar dashboard', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get('/dashboard')
        ->assertRedirect(route('verify-code', absolute: false));

    $this->get(route('verify-code'))
        ->assertOk()
        ->assertSee('Verifica');
});

test('usuario puede verificar su cuenta con código válido', function () {
    $user = User::factory()->unverified()->create();

    $issued = app(EmailVerificationCodeService::class)->issue($user);

    $this->actingAs($user);

    Livewire::test(VerifyCode::class)
        ->set('code', $issued['code'])
        ->call('verify')
        ->assertRedirect(route('dashboard', absolute: false));

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('código expirado muestra error', function () {
    $user = User::factory()->unverified()->create();

    EmailVerificationCode::create([
        'user_id' => $user->id,
        'code_hash' => Hash::make('123456'),
        'sent_to_email' => $user->email,
        'expires_at' => now()->subMinute(),
        'consumed_at' => null,
    ]);

    $this->actingAs($user);

    Livewire::test(VerifyCode::class)
        ->set('code', '123456')
        ->call('verify')
        ->assertHasErrors(['code']);
});
