<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $super = Role::create(['name' => 'super']);
        // $master = Role::create(['name' => 'master']);
        // $admin = Role::create(['name' => 'admin']);
        // $user = Role::create(['name' => 'user']);
        // $staff = Role::create(['name' => 'staff']);
        // $viewer = Role::create(['name' => 'viewer']);

        // $createReport = Permission::create(['name' => 'createReport']);

        $roles = ['super', 'master', 'admin', 'user', 'viewer'];
        $role = Role::firstOrCreate(['name' => 'super']);



        $permissionName =  'Tạo cấu hình';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);


        $permissionName =  'Xem cấu hình';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);

        $permissionName =  'Sửa cấu hình';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);
        $permissionName =  'Xóa cấu hình';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);

        $permissionName =  'Tạo bảng chơi';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);
        $permissionName =  'Cập nhật bảng chơi';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);
        $permissionName =  'Xóa bảng chơi';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);
        

        $permissionName =  'Tạo bảng ghi';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);
        $permissionName =  'Cập nhật bảng ghi';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);
        $permissionName =  'Xóa bảng ghi';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);

        $permissionName =  'Xem tiền xâu';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);

        $permissionName =  'Xem tổng cộng';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);

        $permissionName =  'readTD';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);

        $permissionName =  'Xem tổng tiền';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);
        $permissionName =  'Tạo user';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);
        $permissionName =  'Cập nhật user';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);
        $permissionName =  'Xóa user';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);
        $permissionName =  'Xem danh sách user';
        $permission = Permission::firstOrCreate(['name' => $permissionName]);
        $role->givePermissionTo($permission);
       

        // $role = Role::findByName('super');

        // // Tìm user có id = 3
        // $user = User::findOrFail(31);

        // // Gán role
        // $user->assignRole($role);
    }
}
