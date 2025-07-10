<?php

namespace App\Abstracts;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Inertia\Response as InertiaResponse;

abstract class BaseController extends Controller
{
    /**
     * Handle successful response with redirect
     */
    protected function successResponse(string $message, string $route = null, array $routeParams = []): RedirectResponse
    {
        $redirect = $route ? redirect()->route($route, $routeParams) : back();
        return $redirect->with('success', $message);
    }

    /**
     * Handle error response with redirect
     */
    protected function errorResponse(string $message, string $route = null, array $routeParams = []): RedirectResponse
    {
        $redirect = $route ? redirect()->route($route, $routeParams) : back();
        return $redirect->with('error', $message);
    }

    /**
     * Handle info response with redirect
     */
    protected function infoResponse(string $message, string $route = null, array $routeParams = []): RedirectResponse
    {
        $redirect = $route ? redirect()->route($route, $routeParams) : back();
        return $redirect->with('info', $message);
    }

    /**
     * Handle JSON success response
     */
    protected function jsonSuccessResponse(string $message, array $data = [], int $statusCode = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    /**
     * Handle JSON error response
     */
    protected function jsonErrorResponse(string $message, array $errors = [], int $statusCode = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
        ], $statusCode);
    }

    /**
     * Handle exceptions with consistent error logging and response
     */
    protected function handleException(\Exception $exception, string $operation, array $context = [])
    {
        Log::error("{$operation} sırasında hata oluştu", [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
            'context' => $context,
            'user_id' => auth()->id(),
            'ip' => request()->ip(),
            'url' => request()->fullUrl(),
        ]);

        if (request()->expectsJson()) {
            return $this->jsonErrorResponse($exception->getMessage(), [], 500);
        }

        return $this->errorResponse($exception->getMessage());
    }

    /**
     * Handle custom exceptions
     */
    protected function handleCustomException(\Exception $exception, string $operation, array $context = [])
    {
        Log::warning("{$operation} - özel hata", [
            'error' => $exception->getMessage(),
            'context' => $context,
            'user_id' => auth()->id(),
        ]);

        if (request()->expectsJson()) {
            return $this->jsonErrorResponse($exception->getMessage());
        }

        return $this->errorResponse($exception->getMessage());
    }

    /**
     * Validate pagination parameters
     */
    protected function validatePaginationParams(array $params): array
    {
        return [
            'page' => max(1, (int) ($params['page'] ?? 1)),
            'per_page' => min(100, max(5, (int) ($params['per_page'] ?? 15))),
        ];
    }
}