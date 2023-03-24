<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PersonSeeder::class,
            UserSeeder::class,
            CompanySeeder::class,
            PersonCompanySeeder::class,
            EnumerationSeeder::class,
            ProvinceCitySeeder::class,
            RolePermissionSeeder::class,
            MenuSeeder::class,
        ]);
    }
}
