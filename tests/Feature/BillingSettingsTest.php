<?php

use App\Livewire\Settings\Billing;
use App\Models\Subscription;
use App\Models\User;
use App\SubscriptionTier;
use Livewire\Livewire;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);

test('billing page is displayed for authenticated user', function () {
    $this->actingAs(User::factory()->create(['email_verified_at' => now()]));

    $this->get(route('billing.show'))->assertOk()->assertSee(__('Pagos y Planes'));
});

test('billing page shows plan and history', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'subscription_tier' => SubscriptionTier::Pyme,
    ]);

    $this->actingAs($user);

    $this->get(route('billing.show'))
        ->assertOk()
        ->assertSee($user->resolveSubscriptionTier()->label());
});

test('user with api access can create api key', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'subscription_tier' => SubscriptionTier::Pyme,
    ]);

    $this->actingAs($user);

    $response = Livewire::test(Billing::class)
        ->set('apiKeyName', 'Test Key')
        ->set('apiKeyType', 'full_access')
        ->call('createApiKey');

    $response->assertHasNoErrors();
    expect($response->get('newlyCreatedToken'))->not->toBeNull();
    expect($user->tokens()->count())->toBe(1);
});

test('user can change plan to pyme', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'subscription_tier' => SubscriptionTier::Independent,
    ]);

    $this->actingAs($user);

    Livewire::test(Billing::class)
        ->set('selectedPlanTier', 'pyme')
        ->call('changePlan')
        ->assertHasNoErrors()
        ->assertDispatched('plan-updated');

    $user->refresh();
    expect($user->subscription_tier)->toBe(SubscriptionTier::Pyme);
    expect(Subscription::where('user_id', $user->id)->count())->toBe(1);
});

test('user can cancel subscription', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
        'subscription_tier' => SubscriptionTier::Pyme,
        'subscription_ends_at' => now()->addMonth(),
    ]);

    $this->actingAs($user);

    Livewire::test(Billing::class)->call('cancelSubscription');

    $user->refresh();
    expect($user->subscription_tier)->toBe(SubscriptionTier::Independent);
    expect($user->subscription_ends_at)->toBeNull();
});
