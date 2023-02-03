<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PersonCompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('person_companies')->insert([
            [
                'person_id' => 2,
                'company_id' => 1,
                'start_work_date' => '2023-01-13',
                'suggest_salary' => 0,
                'daily_income' => 0,
                'position' => 'برنامه نویس & co-founder',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
