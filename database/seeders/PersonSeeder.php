<?php

namespace Database\Seeders;

use App\Traits\Common;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PersonSeeder extends Seeder
{
    use Common;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('people')->insert([
            [
                'code' => $this->randomString(),
                'internal_code' => $this->randomPersonnelCode(),
                'name' => 'کاربر',
                'family' => 'ماشین',
                'father_name' => 'تست',
                'national_code' => '1111111111',
                'identity' => '111',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'code' => $this->randomString(),
                'internal_code' => $this->randomPersonnelCode(),
                'name' => 'اسماعیل',
                'family' => 'حیدرزاده',
                'father_name' => 'محمدتقی',
                'national_code' => '0084492945',
                'identity' => '57267',
                'created_by' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
