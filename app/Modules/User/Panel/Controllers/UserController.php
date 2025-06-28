<?php

namespace App\Modules\User\Panel\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\UserService;
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
        $users = $this->userService->getAllUsers();
        
        return Inertia::render('Modules/User/Panel/Pages/Index', [
            'users' => $users
        ]);
    }

    /**
     * Admin paneli kullanıcı detayı
     */
    public function show(int $id): Response
    {
        $user = $this->userService->getUserById($id);
        
        if (!$user) {
            abort(404);
        }
        
        return Inertia::render('Modules/User/Panel/Pages/Show', [
            'user' => $user
        ]);
    }

    /**
     * Kullanıcı düzenleme sayfası
     */
    public function edit(int $id): Response
    {
        $user = $this->userService->getUserById($id);
        
        if (!$user) {
            abort(404);
        }
        
        return Inertia::render('Modules/User/Panel/Pages/Edit', [
            'user' => $user
        ]);
    }

    /**
     * Kullanıcı güncelleme
     */
    public function update(Request $request, int $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id,
        ]);

        $this->userService->updateUser($id, $validated);

        return redirect()->route('panel.users.index')
            ->with('success', 'Kullanıcı başarıyla güncellendi.');
    }

    /**
     * Kullanıcı silme
     */
    public function destroy(int $id)
    {
        $this->userService->deleteUser($id);

        return redirect()->route('panel.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
    }
} 