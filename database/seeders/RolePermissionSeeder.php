<?php

namespace Database\Seeders;

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
        $role = Role::create([
            'code' => $this->randomString(),
            'name' => 'super_admin',
            'caption' => 'سوپر ادمین',
            'guard_name' => 'api'
        ]);
//        $role = Role::find(1);

        $resources = [
            'person',
            'accessory',
            'account',
            'address',
            'attachment',
            'bill',
            'chat',
            'cheque',
            'cloth',
            'company',
            'cutting',
            'design_model',
            'notif',
            'invoice',
            'factor',
            'payment',
            'place',
            'product',
            'rent',
            'request_product_from_warehouse',
            'return_factor',
            'salary',
            'sale_periods',
            'sewing',
            'user',
            'customer',
            'report',
            'role',
        ];

        foreach ($resources as $resource) {
            $permission = Permission::create(['name' => "admin-$resource", 'guard_name' => 'api']);
            $role->givePermissionTo($permission);

            Permission::create(['name' => "edit-$resource", 'guard_name' => 'api']);
            Permission::create(['name' => "view-$resource", 'guard_name' => 'api']);

        }

        $user = User::find(2);
        $user->assignRole($role);
    }
}
