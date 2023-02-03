<?php

namespace Database\Seeders;

use App\Traits\Common;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    use Common;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'person_id' => 1,
                'code' => $this->randomString(),
                'mobile' => '09123456789',
                'password' => Hash::make('09123456789'),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'person_id' => 2,
                'code' => $this->randomString(),
                'mobile' => '09337687219',
                'password' => Hash::make('09337687219'),
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
