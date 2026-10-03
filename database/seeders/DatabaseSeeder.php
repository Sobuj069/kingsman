<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(UnitsTableSeeder::class);
        $this->call(BrandsTableSeeder::class);
        $this->call(BranchesTableSeeder::class);
        // $this->call(CategoriesTableSeeder::class);
        $this->call(CustomersTableSeeder::class);
        $this->call(SuppliersTableSeeder::class);
        // $this->call(ProductsTableSeeder::class);
        $this->call(BankAccountsTableSeeder::class);
        $this->call(BusinessSettingsTableSeeder::class);
        // $this->call(BannersTableSeeder::class);
    }
}
