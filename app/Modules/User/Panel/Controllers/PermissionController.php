<?php

namespace App\Modules\User\Panel\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\PermissionService;
use App\Modules\User\Requests\CreatePermissionRequest;
use App\Modules\User\Requests\UpdatePermissionRequest;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class PermissionController extends Controller
{
    public function __construct(
        private PermissionService $permissionService
    ) {}

    /**
     * Admin paneli yetki listesi
     */
    public function index(): Response
    {
        $permissions = $this->permissionService->getAllPermissionsWithPagination();
        $stats = $this->permissionService->getStats();
        $modules = $this->permissionService->getModules();
        
        return Inertia::render('User/Panel/Permissions/Index', [
            'permissions' => $permissions,
            'stats' => $stats,
            'modules' => $modules
        ]);
    }

    /**
     * Yetki oluşturma sayfası
     */
    public function create(): Response
    {
        $modules = $this->permissionService->getModules();
        
        return Inertia::render('User/Panel/Permissions/Create', [
            'modules' => $modules
        ]);
    }

    /**
     * Yetki oluşturma
     */
    public function store(CreatePermissionRequest $request): Response|RedirectResponse
    {
        $this->permissionService->createPermission($request->validated());
        
        return redirect()->route('panel.permissions.index')
            ->with('success', 'Yetki başarıyla oluşturuldu.');
    }

    /**
     * Admin paneli yetki detayı
     */
    public function show(int $id): Response|RedirectResponse
    {
        $permission = $this->permissionService->getPermissionById($id);
        $users = $this->permissionService->getUsersByPermission($id);
        $roles = $this->permissionService->getRolesByPermission($id);
        
        return Inertia::render('User/Panel/Permissions/Show', [
            'permission' => $permission,
            'users' => $users,
            'roles' => $roles
        ]);
    }

    /**
     * Yetki düzenleme sayfası
     */
    public function edit(int $id): Response|RedirectResponse
    {
        $permission = $this->permissionService->getPermissionById($id);
        $modules = $this->permissionService->getModules();
        
        return Inertia::render('User/Panel/Permissions/Edit', [
            'permission' => $permission,
            'modules' => $modules
        ]);
    }

    /**
     * Yetki güncelleme
     */
    public function update(UpdatePermissionRequest $request, int $id): Response|RedirectResponse
    {
        $this->permissionService->updatePermission($id, $request->validated());
        
        return redirect()->route('panel.permissions.index')
            ->with('success', 'Yetki başarıyla güncellendi.');
    }

    /**
     * Yetki silme
     */
    public function destroy(int $id): Response|RedirectResponse
    {
        $deleted = $this->permissionService->deletePermission($id);
        
        if ($deleted) {
            return redirect()->route('panel.permissions.index')
                ->with('success', 'Yetki başarıyla silindi.');
        }
        
        return redirect()->route('panel.permissions.index')
            ->with('error', 'Sistem yetkileri silinemez.');
    }

    /**
     * Yetki arama
     */
    public function search(Request $request): Response
    {
        $query = $request->get('query', '');
        $permissions = $this->permissionService->searchPermissions($query);
        
        return Inertia::render('User/Panel/Permissions/Index', [
            'permissions' => $permissions,
            'searchQuery' => $query
        ]);
    }

    /**
     * Modüle göre yetkileri getir
     */
    public function byModule(string $module): Response
    {
        $permissions = $this->permissionService->getPermissionsByModule($module);
        
        return Inertia::render('User/Panel/Permissions/Index', [
            'permissions' => $permissions,
            'selectedModule' => $module
        ]);
    }
} 