<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Modules\User\Services\RoleService;
use App\Modules\User\Services\PermissionService;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roleService = app(RoleService::class);
        $permissionService = app(PermissionService::class);

        // Varsayılan yetkileri oluştur
        $permissionService->createDefaultPermissions();

        // Varsayılan rolleri oluştur
        $roleService->createDefaultRoles();

        // Süper admin rolüne tüm yetkileri ata
        $superAdminRole = $roleService->getRoleByName('super-admin');
        if ($superAdminRole) {
            $allPermissions = $permissionService->getAllPermissions();
            $superAdminRole->syncPermissions($allPermissions);
        }

        // Admin rolüne admin yetkilerini ata
        $adminRole = $roleService->getRoleByName('admin');
        if ($adminRole) {
            $adminPermissions = $permissionService->getAllPermissions()->filter(function ($permission) {
                return !in_array($permission->name, ['super-admin']);
            });
            $adminRole->syncPermissions($adminPermissions);
        }

        // Moderatör rolüne moderatör yetkilerini ata
        $moderatorRole = $roleService->getRoleByName('moderator');
        if ($moderatorRole) {
            $moderatorPermissions = $permissionService->getAllPermissions()->filter(function ($permission) {
                return in_array($permission->name, [
                    'user.view',
                    'user.edit',
                    'role.view',
                    'permission.view'
                ]);
            });
            $moderatorRole->syncPermissions($moderatorPermissions);
        }

        // User rolüne temel yetkileri ata
        $userRole = $roleService->getRoleByName('user');
        if ($userRole) {
            $userPermissions = $permissionService->getAllPermissions()->filter(function ($permission) {
                return in_array($permission->name, [
                    'user.view'
                ]);
            });
            $userRole->syncPermissions($userPermissions);
        }
    }
}
