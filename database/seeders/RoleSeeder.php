<?php

namespace Database\Seeders;

use App\Models\Rent;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Carbon\Carbon;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        

        $roles = ['super', 'super_template', 'master_template', 'admin_template'];

        $permissions = [
            'Tạo cấu hình',
            'Xem cấu hình',
            'Sửa cấu hình',
            'Xóa cấu hình',
            'Tạo bảng chơi',
            'Cập nhật bảng chơi',
            'Xóa bảng chơi',
            'Tạo bảng ghi',
            'Cập nhật bảng ghi',
            'Xóa bảng ghi',
            'Xem tiền xâu',
            'Tổng chi của nhiều bảng',
            'Xem tổng chi của bảng hiện tại',
            'Xem tổng tiền',
            'Tạo user',
            'Cập nhật user',
            'Xóa user',
            'Xem danh sách user',
            'Gia hạn',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions); // gán toàn bộ permission một lần
        }

        $roles = ['master','admin','staff','user','viewer'];

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
        }


        $roles = ['user_template'];

        $permissions = [
            'Tạo cấu hình',
            'Xem cấu hình',
            'Sửa cấu hình',
            'Xóa cấu hình',
            'Tạo bảng chơi',
            'Cập nhật bảng chơi',
            'Xóa bảng chơi',
            'Tạo bảng ghi',
            'Cập nhật bảng ghi',
            'Xóa bảng ghi',
            'Xem tiền xâu',
            'Tổng chi của nhiều bảng',
            'Xem tổng chi của bảng hiện tại',
            'Xem tổng tiền',
            'Tạo user',
            'Cập nhật user',
            'Xóa user',
            'Xem danh sách user',
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions); // gán toàn bộ permission một lần
        }
        
        $roles = ['viewer_template'];

        $permissions = [
            
            'Xem tiền xâu',
            'Tổng chi của nhiều bảng',
            'Xem tổng chi của bảng hiện tại',
            'Xem tổng tiền',
            
        ];

        foreach ($permissions as $permissionName) {
            Permission::firstOrCreate(['name' => $permissionName]);
        }

        foreach ($roles as $roleName) {
            $role = Role::firstOrCreate(['name' => $roleName]);
            $role->syncPermissions($permissions); // gán toàn bộ permission một lần
        }
        
        $super = User::factory()->create([
            'name' => 'super',
            'email' => 'super@example.com',
            'username' => 'super',
            'password' => '123123',
        ]);

        $role = Role::findByName('super');

        $super->assignRole($role);

        Rent::create([
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addYears(100),
            'amount' => 0,
            'user_id'=> $super->id,
        ]);

        
    }
}
