<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use App\SubscriptionTier;
use App\SystemRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Usuarios de prueba con credenciales fijas para probar suscripciones, Pagos y Planes y API.
 * Contraseña común: password
 */
class BillingDemoSeeder extends Seeder
{
    public const PASSWORD = 'password';

    public function run(): void
    {
        $this->createUserFreemium();
        $this->createUserPro();
        $this->createUserEnterprise();
    }

    private function createUserFreemium(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'freemium@integraltech.cl'],
            [
                'name' => 'Usuario Freemium',
                'first_name' => 'Usuario',
                'last_name' => 'Freemium',
                'password' => Hash::make(self::PASSWORD),
                'email_verified_at' => now(),
                'system_role' => SystemRole::User,
                'subscription_tier' => SubscriptionTier::Independent,
                'subscription_ends_at' => null,
                'fantasy_name' => 'Freemium Demo',
                'economic_activity' => 'Servicios',
            ]
        );

        $user->forceFill([
            'subscription_tier' => SubscriptionTier::Independent,
            'subscription_ends_at' => null,
        ])->save();
    }

    private function createUserPro(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'pro@integraltech.cl'],
            [
                'name' => 'Usuario Pro',
                'first_name' => 'Usuario',
                'last_name' => 'Pro',
                'password' => Hash::make(self::PASSWORD),
                'email_verified_at' => now(),
                'system_role' => SystemRole::User,
                'subscription_tier' => SubscriptionTier::Pyme,
                'subscription_ends_at' => now()->addYear(),
                'fantasy_name' => 'Pro Demo',
                'economic_activity' => 'Servicios',
            ]
        );

        $user->forceFill([
            'subscription_tier' => SubscriptionTier::Pyme,
            'subscription_ends_at' => now()->addYear(),
        ])->save();

        if (! $user->subscriptions()->where('subscription_tier', SubscriptionTier::Pyme)->exists()) {
            Subscription::create([
                'user_id' => $user->id,
                'subscription_tier' => SubscriptionTier::Pyme,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addMonths(11),
                'amount' => 29900,
                'currency' => 'CLP',
                'payment_method' => 'tarjeta',
                'external_id' => 'demo_pro_'.$user->id,
                'metadata' => ['origen' => 'BillingDemoSeeder', 'plan' => 'Pro'],
            ]);
        }
    }

    private function createUserEnterprise(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'enterprise@integraltech.cl'],
            [
                'name' => 'Usuario Enterprise',
                'first_name' => 'Usuario',
                'last_name' => 'Enterprise',
                'password' => Hash::make(self::PASSWORD),
                'email_verified_at' => now(),
                'system_role' => SystemRole::User,
                'subscription_tier' => SubscriptionTier::Enterprise,
                'subscription_ends_at' => now()->addYear(),
                'fantasy_name' => 'Enterprise Demo',
                'economic_activity' => 'Servicios',
            ]
        );

        $user->forceFill([
            'subscription_tier' => SubscriptionTier::Enterprise,
            'subscription_ends_at' => now()->addYear(),
        ])->save();

        if (! $user->subscriptions()->where('subscription_tier', SubscriptionTier::Enterprise)->exists()) {
            Subscription::create([
                'user_id' => $user->id,
                'subscription_tier' => SubscriptionTier::Enterprise,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addMonths(11),
                'amount' => 99900,
                'currency' => 'CLP',
                'payment_method' => 'tarjeta',
                'external_id' => 'demo_ent_'.$user->id,
                'metadata' => ['origen' => 'BillingDemoSeeder', 'plan' => 'Enterprise'],
            ]);
        }
    }
}
