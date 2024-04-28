<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;


use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // create permissions
        $addUser='add user';
        $editUser='edit user';
        $deleteUser='delete user';
        $getUser='get user';

        $addProduct='add product';
        $editProduct=' edit product';
        $deleteProduct='delete product';
        $viewProduct='view product';

        // create permissions
        Permission::create(['name' =>$addUser]);
        Permission::create(['name' =>$editUser]);
        Permission::create(['name' =>$deleteUser]);
        Permission::create(['name' =>$getUser]);
        Permission::create(['name' =>$addProduct]);
        Permission::create(['name' =>$editProduct]);
        Permission::create(['name' =>$deleteProduct]);
        Permission::create(['name' =>$viewProduct]);

        //define roles available
        $superAdmin='super-admin';
        $systemAdmin ='system-admin';
        $member='member';

        Role::create(['name' => $superAdmin])
            ->givePermissionTo(Permission::all());

        Role::create(['name' => $systemAdmin])
            ->givePermissionTo(
                $addUser,
                $editUser,
            );

        Role::create(['name' => $member])
            ->givePermissionTo(
                $viewProduct,
            );






    }
}
