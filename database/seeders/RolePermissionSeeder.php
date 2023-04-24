<?php

namespace Database\Seeders;

use App\Models\PermissionGroup;
use App\Models\User;
use App\Traits\Common;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    use Common;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        /*$role = Role::create([
            'code' => $this->randomString(),
            'name' => 'super_admin',
            'caption' => 'سوپر ادمین',
            'guard_name' => 'api',
            'created_by' => 1,
        ]);*/
        $role = Role::find(1);

        $resources = [
            /*'person' => 'اشخاص',
            'accessory' => 'خرج کار',
            'account' => 'حساب ها',
            'address' => 'آدرس',
            'attachment' => 'پیوست',
            'bill' => 'قبوض',
            'chat' => 'چت',
            'cheque' => 'چک',
            'cloth' => 'پارچه',
            'company' => 'شرکت',
            'cutting' => 'برش',
            'design_model' => 'طراحی مدل',
            'notif' => 'اعلان',
            'invoice' => 'پیش فاکتور',
            'factor' => 'فاکتور',
            'payment' => 'پرداخت',
            'place' => 'مکان',
            'product' => 'محصول',
            'rent' => 'اجاره',
            'request_product_from_warehouse' => 'درخواست کالا از انبار',
            'return_factor' => 'مرجوع کالا',
            'salary' => 'حقوق',
            'sale_periods' => 'دوره فروش',
            'sewing' => 'دوخت',
            'user' => 'کاربر',
            'customer' => 'مشتری',
            'report' => 'گزارش',*/
            'role' => 'نقش',
            'enumeration' => 'مقادیر',
        ];

        foreach ($resources as $resource => $caption) {
            $permission_group = new PermissionGroup();

            $permission_group->name = $resource;
            $permission_group->caption = $caption;

            $permission_group->save();

            $permission = Permission::create([
                'permission_group_id' => $permission_group->id,
                'name' => "admin-$resource",
                'guard_name' => 'api'
            ]);
            $role->givePermissionTo($permission);

            Permission::create([
                'permission_group_id' => $permission_group->id,
                'name' => "edit-$resource",
                'guard_name' => 'api'
            ]);
            Permission::create([
                'permission_group_id' => $permission_group->id,
                'name' => "view-$resource",
                'guard_name' => 'api'
            ]);

        }

//        $user = User::find(2);
//        $user->assignRole($role);
    }
}
