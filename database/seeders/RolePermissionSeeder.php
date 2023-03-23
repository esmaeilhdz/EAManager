<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'super_admin', 'guard_name' => 'api']);

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
            'swing',
            'user',
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
