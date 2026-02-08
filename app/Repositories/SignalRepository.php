<?php

namespace App\Repositories;

use App\Models\Signal;
use Illuminate\Database\Eloquent\Collection;

class SignalRepository
{
    public function latest(int $limit = 10): Collection
    {
        return Signal::latest()->limit($limit)->get();
    }

    public function create(array $data): ?Signal
    {
        if (Signal::where('fingerprint', $data['fingerprint'])->exists()) {
            return null;
        }

        return Signal::create($data);
    }
}
