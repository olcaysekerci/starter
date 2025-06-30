<?php

namespace App\Modules\User\Panel\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\UserService;
use App\Modules\User\Requests\UpdateUserRequest;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService
    ) {}

    /**
     * Admin paneli kullanıcı listesi
     */
    public function index(): Response
    {
        $users = $this->userService->getAllUsersWithPagination();
        
        return Inertia::render('User/Panel/Dashboard', [
            'users' => $users
        ]);
    }

    /**
     * Admin paneli kullanıcı detayı
     */
    public function show(int $id): Response
    {
        $user = $this->userService->getUserById($id);
        
        return Inertia::render('User/Panel/Show', [
            'user' => $user
        ]);
    }

    /**
     * Kullanıcı düzenleme sayfası
     */
    public function edit(int $id): Response
    {
        $user = $this->userService->getUserById($id);
        
        return Inertia::render('User/Panel/Edit', [
            'user' => $user
        ]);
    }

    /**
     * Kullanıcı güncelleme
     */
    public function update(UpdateUserRequest $request, int $id): Response
    {
        $this->userService->updateUser($id, $request->validated());
        
        return redirect()->route('panel.users.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    /**
     * Kullanıcı silme
     */
    public function destroy(int $id): Response
    {
        $this->userService->deleteUser($id);
        
        return redirect()->route('panel.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }
} 