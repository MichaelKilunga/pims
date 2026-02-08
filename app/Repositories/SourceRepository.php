<?php

namespace App\Repositories;

use App\Models\Source;
use Illuminate\Database\Eloquent\Collection;

class SourceRepository
{
    public function findByDomain(int $domainId): Collection
    {
        return Source::where('domain_id', $domainId)->get();
    }

    public function create(array $data): Source
    {
        return Source::create($data);
    }
}
