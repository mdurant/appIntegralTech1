<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\SystemRole;
use App\TenantRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
        ]);

        User::factory()->guest()->create([
            'name' => 'Guest',
            'email' => 'guest@example.com',
        ]);

        $providers = User::factory()->provider()->count(20)->create();

        User::factory()->client()->count(3)->create()->each(function (User $client) use ($admin, $providers): void {
            $tenant = Tenant::factory()->create([
                'name' => fake()->company(),
                'slug' => Str::slug($client->email, '-'),
                'created_by_user_id' => $admin->id,
            ]);

            $tenant->users()->attach($client->id, ['role' => TenantRole::Owner->value]);

            $client->forceFill(['current_tenant_id' => $tenant->id])->save();

            $providers->random(random_int(1, 3))->each(function (User $provider) use ($tenant): void {
                $tenant->users()->syncWithoutDetaching([
                    $provider->id => ['role' => TenantRole::Member->value],
                ]);
            });
        });
    }
}
