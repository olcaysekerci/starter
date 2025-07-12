<?php

namespace App\Modules\User\Panel\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\Services\UserService;
use App\Modules\User\Requests\CreateUserRequest;
use App\Modules\User\Requests\UpdateUserRequest;
use App\Modules\User\Services\RoleService;
use App\Modules\User\Exceptions\UserException;
use App\Modules\User\Actions\CreateUserAction;
use App\Modules\User\Actions\UpdateUserAction;
use App\Modules\User\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Exception;

class UserController extends Controller
{
    public function __construct(
        private UserService $userService,
        private RoleService $roleService,
        private CreateUserAction $createUserAction,
        private UpdateUserAction $updateUserAction
    ) {}

    /**
     * Admin paneli kullanıcı listesi
     */
    public function index(Request $request): Response
    {
        // Yetki kontrolü
        if (!Gate::allows('view-users')) {
            abort(403, 'Bu işlem için yetkiniz yok.');
        }

        // Rate limiting
        $key = 'user-list-' . $request->ip();
        if (RateLimiter::tooManyAttempts($key, 60)) {
            abort(429, 'Çok fazla istek gönderdiniz. Lütfen bekleyin.');
        }
        RateLimiter::hit($key);

        $search = $request->get('search');
        $perPage = min($request->get('per_page', 15), 100); // Maksimum 100 kayıt
        
        $users = $search 
            ? $this->userService->searchUsers($search, $perPage)
            : $this->userService->getAllUsersWithPagination($perPage);
        
        return Inertia::render('User/Panel/Index', [
            'users' => $users,
            'filters' => [
                'search' => $search,
                'per_page' => $perPage
            ]
        ]);
    }

    /**
     * Kullanıcı arama
     */
    public function search(Request $request): Response
    {
        $query = $request->get('q', '');
        $perPage = $request->get('per_page', 15);
        
        $users = $this->userService->searchUsers($query, $perPage);
        
        return Inertia::render('User/Panel/Index', [
            'users' => $users,
            'filters' => [
                'search' => $query,
                'per_page' => $perPage
            ]
        ]);
    }

    /**
     * Kullanıcı oluşturma sayfası
     */
    public function create(): Response
    {
        $roles = $this->roleService->getAllRoles();
        
        return Inertia::render('User/Panel/Create', [
            'roles' => $roles
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateUserRequest $request): RedirectResponse
    {
        try {
            $user = $this->createUserAction->execute($request->validated());
            
            return redirect()
                ->route('panel.users.index')
                ->with('success', 'Kullanıcı başarıyla oluşturuldu.');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->with('error', 'Kullanıcı oluşturulurken bir hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Admin paneli kullanıcı detayı
     */
    public function show(int $id): Response|RedirectResponse
    {
        try {
            $userDetail = $this->userService->getUserDetailWithStats($id);
        
            return Inertia::render('User/Panel/Show', [
                'user' => $userDetail['user'],
                'stats' => $userDetail['stats']
            ]);
        } catch (UserException $e) {
            return redirect()->route('panel.users.index')
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Kullanıcı düzenleme sayfası
     */
    public function edit(int $id): Response|RedirectResponse
    {
        try {
            $user = $this->userService->getUserDTOById($id);
            $roles = $this->roleService->getAllRoles();
        
        return Inertia::render('User/Panel/Edit', [
                'user' => $user,
                'roles' => $roles
        ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('panel.users.index')
                ->with('error', 'Kullanıcı bulunamadı.');
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        try {
            $user = $this->updateUserAction->execute($user, $request->validated());
            
            return redirect()
                ->route('panel.users.index')
                ->with('success', 'Kullanıcı başarıyla güncellendi.');
        } catch (ValidationException $e) {
            return back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (Exception $e) {
            return back()
                ->with('error', 'Kullanıcı güncellenirken bir hata oluştu: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Kullanıcı silme
     */
    public function destroy(int $id): Response|RedirectResponse
    {
        try {
        $this->userService->deleteUser($id);
        
        return redirect()->route('panel.users.index')
            ->with('success', 'Kullanıcı başarıyla silindi.');
                
        } catch (ModelNotFoundException $e) {
            return redirect()->route('panel.users.index')
                ->with('error', 'Kullanıcı bulunamadı.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Kullanıcı silinirken bir hata oluştu: ' . $e->getMessage());
        }
    }

    /**
     * Kullanıcıya e-posta gönderme sayfası
     */
    public function sendEmail(int $id): Response|RedirectResponse
    {
        try {
            $user = $this->userService->getUserDTOById($id);
            
            return Inertia::render('User/Panel/SendEmail', [
                'user' => $user
            ]);
        } catch (UserException $e) {
            return redirect()->route('panel.users.index')
                ->with('error', $e->getMessage());
        }
    }
} 