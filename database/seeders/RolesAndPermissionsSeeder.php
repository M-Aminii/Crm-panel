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
        $permissions = [
            'manage users', 'view users',
            'manage customers', 'view customers',
            'manage invoices', 'view invoices',
            'manage description_dimension', 'view description_dimension',
            'manage access', 'view access',
            'manage final_order', 'view final_order',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Roles
        $roles = [
            'super-admin' => Permission::all(),
            'sales-manager' => ['manage users', 'view users', 'manage customers', 'view customers', 'view invoices', 'manage access', 'view access', 'manage final_order', 'view final_order','manage description_dimension', 'view description_dimension'],
            'financial-manager' => ['view invoices'],
            'executive-manager' => ['view users', 'view customers', 'view invoices'],
            'sales-expert' => ['manage customers', 'view customers', 'manage invoices', 'view invoices', 'view description_dimension'],
            'financial-expert' => ['view invoices'],
            'executive-expert' => ['view users', 'view customers'],
        ];

        foreach ($roles as $roleName => $rolePermissions) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($rolePermissions);
        }
    }
}
