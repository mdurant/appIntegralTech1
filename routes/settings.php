<?php

use App\Http\Controllers\Settings\ApiKeyController;
use App\Livewire\Settings\ActiveSessions;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Billing;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

Route::middleware(['auth', 'email.code'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::livewire('settings/profile', Profile::class)->name('profile.edit');
    Route::livewire('settings/billing', Billing::class)->name('billing.show');

    Route::get('settings/api-keys', [ApiKeyController::class, 'index'])->name('api-keys.index');
    Route::post('settings/api-keys', [ApiKeyController::class, 'store'])->name('api-keys.store');
    Route::delete('settings/api-keys/{id}', [ApiKeyController::class, 'destroy'])->name('api-keys.destroy');
});

Route::middleware(['auth', 'email.code'])->group(function () {
    Route::livewire('settings/password', Password::class)->name('user-password.edit');
    Route::livewire('settings/appearance', Appearance::class)->name('appearance.edit');

    Route::livewire('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');

    Route::livewire('settings/active-sessions', ActiveSessions::class)->name('active-sessions.show');
});
