<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use App\SystemRole;
use Illuminate\Database\Seeder;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminId = User::query()
            ->where('system_role', SystemRole::Admin->value)
            ->value('id');

        Tenant::factory()->count(2)->create([
            'created_by_user_id' => $adminId,
        ]);
    }
}
