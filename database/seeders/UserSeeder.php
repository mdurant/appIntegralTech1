<?php

namespace Database\Seeders;

use App\Helpers\ChileanDataHelper;
use App\Models\Tenant;
use App\Models\User;
use App\TenantRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Las credenciales demo (admin, cliente, usuario) se crean en DemoCredentialsSeeder.
     * Este seeder usa al admin existente y crea el resto de usuarios (invitado, proveedor, etc.).
     */
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@integraltech.cl')->first();
        if (! $admin) {
            $this->command?->warn('UserSeeder: Ejecutar DemoCredentialsSeeder primero (incluido en DatabaseSeeder).');
            return;
        }

        User::factory()->guest()->create([
            'name' => 'Invitado',
            'email' => 'invitado@integraltech.cl',
        ]);

        User::factory()->provider()->create([
            'name' => 'Proveedor Demo',
            'email' => 'proveedor@integraltech.cl',
        ]);

        $providers = User::factory()->provider()->count(20)->create();

        User::factory()->client()->count(3)->create()->each(function (User $client) use ($admin, $providers): void {
            $tenant = Tenant::factory()->create([
                'name' => ChileanDataHelper::fleteCompanyName(),
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
