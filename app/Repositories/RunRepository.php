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

    public function complete(Run $run, int $findingsCount): void
    {
        $run->update([
            'completed_at' => now(),
            'findings_count' => $findingsCount,
        ]);
    }
}
