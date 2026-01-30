<?php

namespace App\Livewire\Settings;

use App\Models\Subscription;
use App\SubscriptionTier;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Billing extends Component
{
    public ?string $newlyCreatedToken = null;

    public string $apiKeyName = '';

    public string $apiKeyType = 'full_access';

    public ?string $selectedPlanTier = null;

    public function createApiKey(): void
    {
        $user = Auth::user();
        if (! $user->hasApiAccess()) {
            $this->redirect(route('billing.show'));

            return;
        }

        $validated = $this->validate([
            'apiKeyName' => ['required', 'string', 'max:255'],
            'apiKeyType' => ['required', 'string', 'in:full_access,read_only'],
        ], [], [
            'apiKeyName' => __('nombre'),
            'apiKeyType' => __('tipo de clave'),
        ]);

        $token = $user->createToken($validated['apiKeyName'], [$validated['apiKeyType']]);
        $this->newlyCreatedToken = $token->plainTextToken;
        $this->reset('apiKeyName', 'apiKeyType');
    }

    public function revokeApiKey(string $id): void
    {
        $token = Auth::user()->tokens()->findOrFail($id);
        $token->delete();
    }

    public function dismissNewToken(): void
    {
        $this->newlyCreatedToken = null;
    }

    public function changePlan(): void
    {
        $validated = $this->validate([
            'selectedPlanTier' => ['required', 'string', 'in:pyme,enterprise'],
        ], [], [
            'selectedPlanTier' => __('plan'),
        ]);

        $user = Auth::user();
        $tier = SubscriptionTier::from($validated['selectedPlanTier']);

        $user->update([
            'subscription_tier' => $tier,
            'subscription_ends_at' => now()->addYear(),
        ]);

        Subscription::create([
            'user_id' => $user->id,
            'subscription_tier' => $tier,
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
            'amount' => $tier === SubscriptionTier::Enterprise ? 0 : 0,
            'currency' => 'CLP',
            'payment_method' => 'simulado',
            'external_id' => null,
            'metadata' => ['source' => 'upgrade_mvp'],
        ]);

        $this->reset('selectedPlanTier');
        $this->dispatch('plan-updated');
    }

    public function cancelSubscription(): void
    {
        $user = Auth::user();
        if ($user->resolveSubscriptionTier() === SubscriptionTier::Independent) {
            return;
        }

        $user->update([
            'subscription_tier' => SubscriptionTier::Independent,
            'subscription_ends_at' => null,
        ]);

        $this->dispatch('plan-updated');
    }

    public function render()
    {
        $user = Auth::user();
        $subscriptionTier = $user->resolveSubscriptionTier();
        $subscriptions = $user->subscriptions()
            ->orderByDesc('starts_at')
            ->limit(20)
            ->get();

        $apiKeys = $user->tokens()
            ->orderByDesc('created_at')
            ->get()
            ->map(fn ($token) => [
                'id' => $token->id,
                'name' => $token->name,
                'type' => in_array('full_access', $token->abilities ?? []) ? 'full_access' : 'read_only',
                'created_at' => $token->created_at,
            ]);

        return view('livewire.settings.billing', [
            'subscriptionTier' => $subscriptionTier,
            'subscriptionEndsAt' => $user->subscription_ends_at,
            'subscriptions' => $subscriptions,
            'hasApiAccess' => $subscriptionTier->hasApiAccess(),
            'apiKeys' => $apiKeys,
        ]);
    }
}
