<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });

        // Model Not Found Exception Handler
        $this->renderable(function (ModelNotFoundException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Kayıt bulunamadı.',
                    'error' => 'not_found'
                ], 404);
            }

            if ($request->is('panel/*') && \Route::has('panel.dashboard')) {
                return redirect()->route('panel.dashboard')
                    ->with('error', 'Aradığınız kayıt bulunamadı.');
            }

            return redirect()->route('dashboard')
                ->with('error', 'Aradığınız kayıt bulunamadı.');
        });

        // Validation Exception Handler
        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Doğrulama hatası.',
                    'errors' => $e->errors()
                ], 422);
            }

            // Inertia.js için validation hatalarını döndür
            if ($request->header('X-Inertia')) {
                return Inertia::render('Error', [
                    'status' => 422,
                    'message' => 'Doğrulama hatası.',
                    'errors' => $e->errors()
                ])->toResponse($request)->setStatusCode(422);
            }

            return back()->withErrors($e->errors())->withInput();
        });

        // Not Found Exception Handler
        $this->renderable(function (NotFoundHttpException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Sayfa bulunamadı.',
                    'error' => 'not_found'
                ], 404);
            }

            if ($request->header('X-Inertia')) {
                return Inertia::render('Error', [
                    'status' => 404,
                    'message' => 'Sayfa bulunamadı.'
                ])->toResponse($request)->setStatusCode(404);
            }

            return response()->view('errors.404', [], 404);
        });

        // Authentication Exception Handler
        $this->renderable(function (AuthenticationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Kimlik doğrulama gerekli.',
                    'error' => 'unauthenticated'
                ], 401);
            }

            if ($request->header('X-Inertia')) {
                return Inertia::render('Auth/Login', [
                    'message' => 'Lütfen giriş yapın.'
                ])->toResponse($request)->setStatusCode(401);
            }

            return redirect()->route('login')
                ->with('error', 'Lütfen giriş yapın.');
        });
    }
} 