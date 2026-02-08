<?php

namespace App\Repositories;

use App\Models\Run;

class RunRepository
{
    public function start(string $type): Run
    {
        return Run::create([
            'type' => $type,
            'status' => 'running',
            'started_at' => now()
        ]);
    }

    public function update(Run $run, array $data): void
    {
        $run->update($data);
    }

    public function complete(Run $run, int $findingsCount, array $meta = []): void
    {
        $run->update([
            'status' => 'completed',
            'completed_at' => now(),
            'findings_count' => $findingsCount,
            'meta' => array_merge($run->meta ?? [], $meta),
        ]);
    }

    public function fail(Run $run, string $error): void
    {
        $run->update([
            'status' => 'failed',
            'completed_at' => now(),
            'meta' => array_merge($run->meta ?? [], ['error' => $error]),
        ]);
    }
}
