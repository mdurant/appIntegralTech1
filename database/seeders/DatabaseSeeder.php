<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RegionSeeder::class,
            CommuneSeeder::class,
            BankSeeder::class,
            AccountTypeSeeder::class,
            SystemSettingSeeder::class,
            DemoCredentialsSeeder::class,
            UserSeeder::class,
            BillingDemoSeeder::class,
            SubscriptionSeeder::class,
            TenantSeeder::class,
            ServiceCategorySeeder::class,
            ServiceFormFieldSeeder::class,
            ServiceRequestSeeder::class,
            ServiceBidSeeder::class,
            WorkOrderSeeder::class,
        ]);
    }
}
