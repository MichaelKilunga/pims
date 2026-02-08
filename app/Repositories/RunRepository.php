<?php

namespace App\Repositories;

use App\Models\Run;

class RunRepository
{
    public function start(): Run
    {
        return Run::create(['started_at' => now()]);
    }

    public function complete(Run $run, int $findingsCount): void
    {
        $run->update([
            'completed_at' => now(),
            'findings_count' => $findingsCount,
        ]);
    }
}
