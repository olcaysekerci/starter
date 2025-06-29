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
        
        return Inertia::render('Modules/User/Web/Pages/Index', [
            'users' => $users
        ]);
    }

    /**
     * Kullanıcı detay sayfası
     */
    public function show(int $id): Response
    {
        $user = $this->userService->getUserById($id);
        
        return Inertia::render('Modules/User/Web/Pages/Show', [
            'user' => $user
        ]);
    }

    public function profile(): Response
    {
        $user = auth()->user();
        
        return Inertia::render('Modules/User/Web/Pages/Profile', [
            'user' => $user
        ]);
    }
} 