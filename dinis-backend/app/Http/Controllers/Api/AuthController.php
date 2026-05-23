<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\DTOs\Auth\LoginDTO;
use App\Services\Auth\AuthService;
use Exception;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService
    ) {}

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'device_name' => 'nullable|string' 
        ]);

        $dto = LoginDTO::fromRequest($validated);

        try {
            $result = $this->authService->login($dto);

            return response()->json([
                'message' => 'Authentication successful',
                'data' => $result
            ], 200);

        } catch (Exception $e) {
            $statusCode = $e->getCode() ?: 401; 
            return response()->json([
                'error' => $e->getMessage()
            ], $statusCode);
        }
    }

    public function logout(Request $request): JsonResponse
    {
        /** @var \App\Models\SystemUser $user */
        $user = $request->user();
        
        $this->authService->logout($user);

        return response()->json([
            'message' => 'Successfully logged out'
        ], 200);
    }

    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => $request->user()->load(['roles', 'officeLocation'])
        ], 200);
    }
}
