<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * NOTA: Este seeder requiere que spatie/laravel-permission esté instalado.
     * Ejecutar después de: composer require spatie/laravel-permission
     */
    public function run(): void
    {
        // Verificar si el paquete está instalado
        if (! class_exists(\Spatie\Permission\Models\Role::class)) {
            $this->command->warn('Spatie Laravel Permission no está instalado. Saltando seeder.');
            $this->command->info('Para instalar: composer require spatie/laravel-permission');

            return;
        }

        // Crear roles
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin']);
        $clientRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'client']);
        $providerRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'provider']);
        $guestRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'guest']);

        // Crear permisos
        $permissions = [
            // Permisos de Cliente
            'view-own-requests',
            'create-requests',
            'manage-own-requests',
            'view-own-work-orders',
            'create-ratings',

            // Permisos de Profesional
            'view-published-requests',
            'create-bids',
            'manage-own-bids',
            'view-own-work-orders',
            'update-professional-profile',

            // Permisos de Admin
            'manage-all',
            'configure-system',
            'view-analytics',
            'manage-users',
            'manage-tenants',
            'manage-categories',
            'manage-form-fields',
        ];

        foreach ($permissions as $permission) {
            \Spatie\Permission\Models\Permission::firstOrCreate(['name' => $permission]);
        }

        // Asignar permisos a roles
        $adminRole->givePermissionTo(\Spatie\Permission\Models\Permission::all());

        $clientRole->givePermissionTo([
            'view-own-requests',
            'create-requests',
            'manage-own-requests',
            'view-own-work-orders',
            'create-ratings',
        ]);

        $providerRole->givePermissionTo([
            'view-published-requests',
            'create-bids',
            'manage-own-bids',
            'view-own-work-orders',
            'update-professional-profile',
        ]);

        $guestRole->givePermissionTo([
            'view-published-requests',
        ]);

        $this->command->info('Roles y permisos creados exitosamente.');
    }
}
