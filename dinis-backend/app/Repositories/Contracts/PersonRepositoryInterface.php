<?php

namespace App\Repositories\Contracts;

use App\Models\Person;

interface PersonRepositoryInterface
{
    public function create(array $data): Person;
    public function findById(string $id): ?Person;
    public function findByUin(string $uin): ?Person;
    public function getMaxUin(): ?string; // Added this line
}