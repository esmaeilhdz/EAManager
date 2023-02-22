<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EnumerationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('enumerations')->insert([
            [
                'category_name' => 'place_kind',
                'category_caption' => 'نوع مکان',
                'enum_caption' => 'کارگاه داخلی',
                'enum_id' => 1,
                'is_editable' => 1,
                'created_by' => 1,
            ],
            [
                'category_name' => 'place_kind',
                'category_caption' => 'نوع مکان',
                'enum_caption' => 'کارگاه مزدی دوز',
                'enum_id' => 2,
                'is_editable' => 1,
                'created_by' => 1,
            ],
            [
                'category_name' => 'place_kind',
                'category_caption' => 'نوع مکان',
                'enum_caption' => 'انبار',
                'enum_id' => 3,
                'is_editable' => 1,
                'created_by' => 1,
            ],
            [
                'category_name' => 'place_attribute',
                'category_caption' => 'خصوصیات مکان',
                'enum_caption' => 'تعداد چرخ خیاطی',
                'enum_id' => 1,
                'is_editable' => 1,
                'created_by' => 1,
            ],
            [
                'category_name' => 'color',
                'category_caption' => 'رنگ',
                'enum_caption' => 'مشکی',
                'enum_id' => 1,
                'is_editable' => 1,
                'created_by' => 1,
            ],
            [
                'category_name' => 'notification_level',
                'category_caption' => 'سطح اعلان',
                'enum_caption' => 'کم',
                'enum_id' => 1,
                'is_editable' => 0,
                'created_by' => 1,
            ],
            [
                'category_name' => 'notification_level',
                'category_caption' => 'سطح اعلان',
                'enum_caption' => 'متوسط',
                'enum_id' => 2,
                'is_editable' => 0,
                'created_by' => 1,
            ],
            [
                'category_name' => 'notification_level',
                'category_caption' => 'سطح اعلان',
                'enum_caption' => 'زیاد',
                'enum_id' => 3,
                'is_editable' => 0,
                'created_by' => 1,
            ],
            [
                'category_name' => 'notification_level',
                'category_caption' => 'سطح اعلان',
                'enum_caption' => 'فوری',
                'enum_id' => 4,
                'is_editable' => 0,
                'created_by' => 1,
            ],
            [
                'category_name' => 'factor_status',
                'category_caption' => 'وضعیت فاکتور',
                'enum_caption' => 'ناقص',
                'enum_id' => 1,
                'is_editable' => 0,
                'created_by' => 1,
            ],
            [
                'category_name' => 'factor_status',
                'category_caption' => 'وضعیت فاکتور',
                'enum_caption' => 'کامل',
                'enum_id' => 2,
                'is_editable' => 0,
                'created_by' => 1,
            ],
            [
                'category_name' => 'factor_status',
                'category_caption' => 'وضعیت فاکتور',
                'enum_caption' => 'مرجوع',
                'enum_id' => 3,
                'is_editable' => 0,
                'created_by' => 1,
            ],
            [
                'category_name' => 'attachment_type',
                'category_caption' => 'نوع پیوست',
                'enum_caption' => 'عکس پرسنلی',
                'enum_id' => 1,
                'is_editable' => 0,
                'created_by' => 1,
            ],
            [
                'category_name' => 'bill_type',
                'category_caption' => 'نوع قبض',
                'enum_caption' => 'قبض گاز',
                'enum_id' => 1,
                'is_editable' => 0,
                'created_by' => 1,
            ],
            [
                'category_name' => 'bill_type',
                'category_caption' => 'نوع قبض',
                'enum_caption' => 'قبض آب',
                'enum_id' => 2,
                'is_editable' => 0,
                'created_by' => 1,
            ],
            [
                'category_name' => 'bill_type',
                'category_caption' => 'نوع قبض',
                'enum_caption' => 'قبض تلفن',
                'enum_id' => 3,
                'is_editable' => 0,
                'created_by' => 1,
            ],
            [
                'category_name' => 'payment_type',
                'category_caption' => 'نوع پرداخت',
                'enum_caption' => 'حقوق',
                'enum_id' => 1,
                'is_editable' => 1,
                'created_by' => 1,
            ],
            [
                'category_name' => 'payment_type',
                'category_caption' => 'نوع پرداخت',
                'enum_caption' => 'پیک',
                'enum_id' => 2,
                'is_editable' => 1,
                'created_by' => 1,
            ],
            [
                'category_name' => 'payment_type',
                'category_caption' => 'نوع پرداخت',
                'enum_caption' => 'خرید پارچه',
                'enum_id' => 3,
                'is_editable' => 1,
                'created_by' => 1,
            ],
            [
                'category_name' => 'payment_type',
                'category_caption' => 'نوع پرداخت',
                'enum_caption' => 'خرید خرج کار',
                'enum_id' => 4,
                'is_editable' => 1,
                'created_by' => 1,
            ],
            [
                'category_name' => 'payment_type',
                'category_caption' => 'نوع پرداخت',
                'enum_caption' => 'اجاره',
                'enum_id' => 5,
                'is_editable' => 1,
                'created_by' => 1,
            ],
        ]);
    }
}
