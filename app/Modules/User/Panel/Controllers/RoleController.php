<?php

namespace App\Modules\User\Panel\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\RoleService;
use App\Modules\User\Requests\CreateRoleRequest;
use App\Modules\User\Requests\UpdateRoleRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class RoleController extends Controller
{
    public function __construct(
        private RoleService $roleService
    ) {}

    /**
     * Admin paneli rol listesi
     */
    public function index(): Response
    {
        $roles = $this->roleService->getAllRolesWithPagination();
        $stats = $this->roleService->getStats();
        
        return Inertia::render('User/Panel/Roles/Dashboard', [
            'roles' => $roles,
            'stats' => $stats
        ]);
    }

    /**
     * Rol oluşturma sayfası
     */
    public function create(): Response
    {
        $permissions = $this->roleService->getPermissionsByModules();
        
        return Inertia::render('User/Panel/Roles/Create', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Rol oluşturma
     */
    public function store(CreateRoleRequest $request): Response
    {
        $this->roleService->createRole($request->validated());
        
        return redirect()->route('panel.roles.index')
            ->with('success', 'Rol başarıyla oluşturuldu.');
    }

    /**
     * Admin paneli rol detayı
     */
    public function show(int $id): Response
    {
        $role = $this->roleService->getRoleById($id);
        $users = $this->roleService->getUsersByRole($id);
        
        return Inertia::render('User/Panel/Roles/Show', [
            'role' => $role,
            'users' => $users
        ]);
    }

    /**
     * Rol düzenleme sayfası
     */
    public function edit(int $id): Response
    {
        $role = $this->roleService->getRoleById($id);
        $permissions = $this->roleService->getPermissionsByModules();
        
        return Inertia::render('User/Panel/Roles/Edit', [
            'role' => $role,
            'permissions' => $permissions
        ]);
    }

    /**
     * Rol güncelleme
     */
    public function update(UpdateRoleRequest $request, int $id): Response
    {
        $this->roleService->updateRole($id, $request->validated());
        
        return redirect()->route('panel.roles.index')
            ->with('success', 'Rol başarıyla güncellendi.');
    }

    /**
     * Rol silme
     */
    public function destroy(int $id): Response
    {
        $deleted = $this->roleService->deleteRole($id);
        
        if ($deleted) {
            return redirect()->route('panel.roles.index')
                ->with('success', 'Rol başarıyla silindi.');
        }
        
        return redirect()->route('panel.roles.index')
            ->with('error', 'Sistem rolleri silinemez.');
    }

    /**
     * Rol arama
     */
    public function search(Request $request): Response
    {
        $query = $request->get('query', '');
        $roles = $this->roleService->searchRoles($query);
        
        return Inertia::render('User/Panel/Roles/Dashboard', [
            'roles' => $roles,
            'searchQuery' => $query
        ]);
    }
} 