<?php

namespace App\Modules\User\Web\Controllers;

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
     * Kullanıcı listesi sayfası 
     */
    public function index(): Response
    {
        $users = $this->userService->getAllUsers();
        
        return Inertia::render('User/Web/Index', [
            'users' => $users
        ]);
    }

    /**
     * Kullanıcı detay sayfası
     */
    public function show(int $id): Response
    {
        $user = $this->userService->getUserById($id);
        
        return Inertia::render('User/Web/Show', [
            'user' => $user
        ]);
    }

    public function profile(): Response
    {
        $user = auth()->user();
        
        return Inertia::render('User/Web/Profile', [
            'user' => $user
        ]);
    }
} 