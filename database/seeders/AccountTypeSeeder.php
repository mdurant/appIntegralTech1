<?php

namespace Database\Seeders;

use App\Models\AccountType;
use Illuminate\Database\Seeder;

class AccountTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            [
                'name' => 'Cuenta Corriente',
                'code' => 'CC',
                'description' => 'Cuenta corriente bancaria',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Cuenta Vista',
                'code' => 'CV',
                'description' => 'Cuenta vista bancaria',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Cuenta de Ahorro',
                'code' => 'CA',
                'description' => 'Cuenta de ahorro bancaria',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($types as $type) {
            AccountType::updateOrCreate(
                ['code' => $type['code']],
                $type
            );
        }
    }
}
