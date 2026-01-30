<?php

namespace Database\Seeders;

use App\Models\Subscription;
use App\Models\User;
use App\SubscriptionTier;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    /**
     * Seed subscription history with example data in Spanish (dummy).
     */
    public function run(): void
    {
        $providers = User::query()
            ->where('system_role', 'user')
            ->limit(8)
            ->get();

        if ($providers->isEmpty()) {
            return;
        }

        $tiers = [
            ['tier' => SubscriptionTier::Independent, 'amount' => null, 'payment' => null],
            ['tier' => SubscriptionTier::Pyme, 'amount' => 29900, 'payment' => 'tarjeta'],
            ['tier' => SubscriptionTier::Pyme, 'amount' => 29900, 'payment' => 'paypal'],
            ['tier' => SubscriptionTier::Enterprise, 'amount' => 99900, 'payment' => 'tarjeta'],
        ];

        foreach ($providers->take(4) as $index => $user) {
            $config = $tiers[$index % count($tiers)];
            $startsAt = now()->subMonths(2)->startOfMonth();
            $endsAt = $config['tier'] === SubscriptionTier::Independent
                ? null
                : $startsAt->copy()->addMonth();

            $user->forceFill(['subscription_tier' => $config['tier']])->save();

            Subscription::create([
                'user_id' => $user->id,
                'subscription_tier' => $config['tier'],
                'starts_at' => $startsAt,
                'ends_at' => $endsAt,
                'amount' => $config['amount'],
                'currency' => 'CLP',
                'payment_method' => $config['payment'],
                'external_id' => $config['payment'] ? 'sim_'.$user->id.'_'.$startsAt->format('Ymd') : null,
                'metadata' => ['origen' => 'seeder', 'descripcion' => 'Suscripción de ejemplo'],
            ]);
        }

        $extra = $providers->skip(4)->first();
        if ($extra) {
            $extra->forceFill(['subscription_tier' => SubscriptionTier::Enterprise])->save();
            Subscription::create([
                'user_id' => $extra->id,
                'subscription_tier' => SubscriptionTier::Enterprise,
                'starts_at' => now()->subMonth(),
                'ends_at' => now()->addMonths(2),
                'amount' => 99900,
                'currency' => 'CLP',
                'payment_method' => 'tarjeta',
                'external_id' => 'sim_ent_'.$extra->id,
                'metadata' => ['plan' => 'Enterprise', 'descripcion' => 'Plan empresarial mensual'],
            ]);
        }
    }
}
