<?php

namespace App\Repositories\Eloquent;

use App\Models\Person;
use App\Repositories\Contracts\PersonRepositoryInterface;

class EloquentPersonRepository implements PersonRepositoryInterface
{
    public function create(array $data): Person
    {
        return Person::create($data);
    }

    public function findById(string $id): ?Person
    {
        return Person::find($id);
    }

    public function findByUin(string $uin): ?Person
    {
        return Person::where('uin', $uin)->first();
    }

    public function getMaxUin(): ?string
    {
        return Person::max('uin');
    }
}