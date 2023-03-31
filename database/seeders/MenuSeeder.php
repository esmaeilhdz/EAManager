<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('menu')->insert([
            [
                'id' => 1,
                'caption' => 'مقادیر اولیه',
                'route_name' => 'basic',
                'icon' => 'fa fa-home',
                'has_permission' => 0,
            ],
        ]);

        DB::table('menu')->insert([
            [
                'id' => 2,
                'parent_id' => 1,
                'caption' => 'اشخاص',
                'route_name' => 'person',
                'icon' => 'fa fa-person',
            ],
            [
                'id' => 3,
                'parent_id' => 1,
                'caption' => 'کاربران',
                'route_name' => 'user',
                'icon' => 'fa fa-person',
            ],
            [
                'id' => 4,
                'parent_id' => 1,
                'caption' => 'شرکت',
                'route_name' => 'company',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 5,
                'parent_id' => 1,
                'caption' => 'مکان ها',
                'route_name' => 'place',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 6,
                'parent_id' => 1,
                'caption' => 'دوره های فروش',
                'route_name' => 'sale_periods',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 7,
                'parent_id' => 1,
                'caption' => 'حساب ها',
                'route_name' => 'account',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 8,
                'parent_id' => 1,
                'caption' => 'مشتری ها',
                'route_name' => 'customer',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 9,
                'parent_id' => 1,
                'caption' => 'قبوض',
                'route_name' => 'bill',
                'icon' => 'fa fa-building',
            ],
        ]);

        DB::table('menu')->insert([
            [
                'id' => 10,
                'caption' => 'اجرا',
                'route_name' => 'execute',
                'icon' => 'fa fa-building',
                'has_permission' => 0,
            ],
        ]);

        DB::table('menu')->insert([
            [
                'id' => 11,
                'parent_id' => 10,
                'caption' => 'پارچه',
                'route_name' => 'cloth',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 12,
                'parent_id' => 10,
                'caption' => 'برش',
                'route_name' => 'cutting',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 13,
                'parent_id' => 10,
                'caption' => 'دوخت',
                'route_name' => 'sewings',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 14,
                'parent_id' => 10,
                'caption' => 'خرج کار',
                'route_name' => 'accessory',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 15,
                'parent_id' => 10,
                'caption' => 'درخواست کالا از انبار',
                'route_name' => 'request_product_from_warehouse',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 16,
                'parent_id' => 10,
                'caption' => 'طراحی مدل',
                'route_name' => 'design_model',
                'icon' => 'fa fa-building',
            ],
        ]);

        DB::table('menu')->insert([
            [
                'id' => 17,
                'caption' => 'فروش',
                'route_name' => 'sell',
                'icon' => 'fa fa-money',
                'has_permission' => 0,
            ],
        ]);

        DB::table('menu')->insert([
            [
                'id' => 18,
                'parent_id' => 17,
                'caption' => 'پیش فاکتور',
                'route_name' => 'invoice',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 19,
                'parent_id' => 17,
                'caption' => 'فاکتور',
                'route_name' => 'factor',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 20,
                'parent_id' => 17,
                'caption' => 'مرجوع فاکتور',
                'route_name' => 'return_factor',
                'icon' => 'fa fa-building',
            ],
        ]);

        DB::table('menu')->insert([
            [
                'id' => 21,
                'caption' => 'مالی',
                'route_name' => 'financial',
                'icon' => 'fa fa-money',
                'has_permission' => 0,
            ],
        ]);

        DB::table('menu')->insert([
            [
                'id' => 22,
                'parent_id' => 21,
                'caption' => 'چک',
                'route_name' => 'cheque',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 23,
                'parent_id' => 21,
                'caption' => 'اجاره',
                'route_name' => 'rent',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 24,
                'parent_id' => 21,
                'caption' => 'پرداخت',
                'route_name' => 'payment',
                'icon' => 'fa fa-building',
            ],
            [
                'id' => 25,
                'parent_id' => 21,
                'caption' => 'حقوق',
                'route_name' => 'salary',
                'icon' => 'fa fa-building',
            ],
        ]);

        DB::table('menu')->insert([
            [
                'id' => 26,
                'caption' => 'گزارش',
                'route_name' => 'report',
                'icon' => 'fa fa-report',
                'has_permission' => 0,
            ],
        ]);

        DB::table('menu')->insert([
            [
                'id' => 27,
                'parent_id' => 26,
                'caption' => 'مشتریان بدهکار',
                'route_name' => 'report',
                'icon' => 'fa fa-building',
            ],
        ]);
    }
}
