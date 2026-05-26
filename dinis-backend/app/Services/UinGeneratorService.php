<?php

namespace App\Services;

use App\Repositories\Contracts\PersonRepositoryInterface;

class UinGeneratorService
{
    public function __construct(
        private readonly PersonRepositoryInterface $personRepo
    ) {}

    /**
     * Generates a 10-digit UIN (e.g., 1000000001)
     */
    public function generateUin(): string
    {
        $latest = $this->personRepo->getMaxUin(); 
        $next = $latest ? ((int)$latest + 1) : 1000000000;
        
        return (string) $next;
    }

    /**
     * Generates a standard NRC format (e.g., 123456/10/1)
     */
    public function generateNrcNumber(int $provinceCode): string
    {
        $random = str_pad((string) random_int(1, 999999), 6, '0', STR_PAD_LEFT);
        $version = 1; 
        
        return "{$random}/{$provinceCode}/{$version}";
    }
}