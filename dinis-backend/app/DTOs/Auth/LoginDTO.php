<?php

namespace App\DTOs\Auth;

readonly class LoginDTO
{
    public function __construct(
        public string $username,
        public string $password,
        public string $device_name,
    ) {}

    public static function fromRequest(array $validated): self
    {
        return new self(
            username: $validated['username'],
            password: $validated['password'],
            device_name: $validated['device_name'] ?? 'web',
        );
    }
}
