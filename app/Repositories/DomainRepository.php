<?php

namespace App\Repositories;

use App\Models\Domain;
use Illuminate\Database\Eloquent\Collection;

class DomainRepository
{
    public function all(): Collection
    {
        return Domain::all();
    }

    public function find(int $id): ?Domain
    {
        return Domain::find($id);
    }

    public function create(array $data): Domain
    {
        return Domain::create($data);
    }
}
