<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class ProvinceCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('provinces')->insert([
            [
                'name' => 'اردبيل',
            ],
            [
                'name' => 'اصفهان',
            ],
            [
                'name' => 'البرز',
            ],
            [
                'name' => 'ايلام',
            ],
            [
                'name' => 'آذربايجان شرقی',
            ],
            [
                'name' => 'آذربايجان غربی',
            ],
            [
                'name' => 'بوشهر',
            ],
            [
                'name' => 'تهران',
            ],
            [
                'name' => 'چهارمحال وبختياری',
            ],
            [
                'name' => 'خراسان جنوبی',
            ],
            [
                'name' => 'خراسان رضوی',
            ],
            [
                'name' => 'خراسان شمالی',
            ],
            [
                'name' => 'خوزستان',
            ],
            [
                'name' => 'زنجان',
            ],
            [
                'name' => 'سمنان',
            ],
            [
                'name' => 'سيستان وبلوچستان',
            ],
            [
                'name' => 'فارس',
            ],
            [
                'name' => 'قزوین',
            ],
            [
                'name' => 'قم',
            ],
            [
                'name' => 'كردستان',
            ],
            [
                'name' => 'كرمان',
            ],
            [
                'name' => 'كرمانشاه',
            ],
            [
                'name' => 'كهگيلويه وبويراحمد',
            ],
            [
                'name' => 'گلستان',
            ],
            [
                'name' => 'گيلان',
            ],
            [
                'name' => 'لرستان',
            ],
            [
                'name' => 'مازندران',
            ],
            [
                'name' => 'مركزی',
            ],
            [
                'name' => 'هرمزگان',
            ],
            [
                'name' => 'همدان',
            ],
            [
                'name' => 'یزد',
            ]
        ]);

        Artisan::call('import_cities');

    }
}
