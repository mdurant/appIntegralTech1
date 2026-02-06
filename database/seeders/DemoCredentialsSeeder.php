<?php

namespace Database\Seeders;

use App\Helpers\ChileanDataHelper;
use App\Models\Tenant;
use App\Models\User;
use App\SystemRole;
use App\TenantRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Crea o actualiza las 3 credenciales demo mostradas en el login:
 * - admin@integraltech.cl (Perfil Admin)
 * - cliente@integraltech.cl (Perfil Cliente)
 * - usuario@integraltech.cl (Perfil Usuario / proveedor)
 *
 * Contraseña para los tres: password
 *
 * Se puede ejecutar solo: php artisan db:seed --class=DemoCredentialsSeeder
 */
class DemoCredentialsSeeder extends Seeder
{
    private const PASSWORD = 'password';

    public function run(): void
    {
        $this->seedAdmin();
        $this->seedUsuario();
        $this->seedCliente();
    }

    private function seedAdmin(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'admin@integraltech.cl'],
            [
                'name' => 'Administrador',
                'first_name' => 'Administrador',
                'last_name' => '',
                'password' => Hash::make(self::PASSWORD),
                'system_role' => SystemRole::Admin->value,
                'current_tenant_id' => null,
                'rut' => ChileanDataHelper::chileanRut(),
                'giro_sii' => null,
                'gender' => 'hombre',
                'birth_date' => '1980-01-15',
                'fantasy_name' => null,
                'economic_activity' => null,
                'region_id' => null,
                'commune_id' => null,
            ]
        );
        $user->forceFill(['email_verified_at' => now()])->save();
    }

    private function seedUsuario(): void
    {
        $user = User::query()->updateOrCreate(
            ['email' => 'usuario@integraltech.cl'],
            [
                'name' => 'Usuario Demo',
                'first_name' => 'Usuario',
                'last_name' => 'Demo',
                'password' => Hash::make(self::PASSWORD),
                'system_role' => SystemRole::User->value,
                'current_tenant_id' => null,
                'rut' => ChileanDataHelper::chileanRut(),
                'giro_sii' => ChileanDataHelper::giroSii(),
                'gender' => 'hombre',
                'birth_date' => '1985-06-20',
                'fantasy_name' => ChileanDataHelper::fleteCompanyName(),
                'economic_activity' => 'Servicios técnicos y cotizaciones',
                'region_id' => null,
                'commune_id' => null,
            ]
        );
        $user->forceFill(['email_verified_at' => now()])->save();
    }

    private function seedCliente(): void
    {
        $admin = User::query()->where('email', 'admin@integraltech.cl')->first();
        if (! $admin) {
            $this->command?->warn('DemoCredentialsSeeder: admin no existe. Ejecutar seed completo o crear admin primero.');
            return;
        }

        $cliente = User::query()->updateOrCreate(
            ['email' => 'cliente@integraltech.cl'],
            [
                'name' => 'Cliente Demo',
                'first_name' => 'Cliente',
                'last_name' => 'Demo',
                'password' => Hash::make(self::PASSWORD),
                'system_role' => SystemRole::Client->value,
                'current_tenant_id' => null,
                'rut' => ChileanDataHelper::chileanRut(),
                'giro_sii' => null,
                'gender' => 'hombre',
                'birth_date' => '1990-03-10',
                'fantasy_name' => null,
                'economic_activity' => null,
                'region_id' => null,
                'commune_id' => null,
            ]
        );

        $tenant = Tenant::query()->firstOrCreate(
            ['slug' => 'cliente-integraltech-cl'],
            [
                'name' => ChileanDataHelper::fleteCompanyName(),
                'created_by_user_id' => $admin->id,
            ]
        );

        $tenant->users()->syncWithoutDetaching([
            $cliente->id => ['role' => TenantRole::Owner->value],
        ]);

        $cliente->forceFill([
            'email_verified_at' => now(),
            'current_tenant_id' => $tenant->id,
        ])->save();
    }
}
