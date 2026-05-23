<?php

namespace App\Services\Auth;

use App\DTOs\Auth\LoginDTO;
use App\Models\SystemUser;
use Illuminate\Support\Facades\Hash;
use Exception;

class AuthService
{
    public function login(LoginDTO $dto): array
    {
        $user = SystemUser::where('username', $dto->username)->first();

        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw new Exception("Invalid username or password.", 401);
        }

        if ($user->status !== 'active') {
            throw new Exception("Account is {$user->status}. Please contact the system administrator.", 403);
        }

        $token = $user->createToken($dto->device_name)->plainTextToken;

        return [
            'user' => $user->load('roles'), 
            'token' => $token
        ];
    }

    public function logout(SystemUser $user): void
    {
        $user->currentAccessToken()->delete();
    }
}
