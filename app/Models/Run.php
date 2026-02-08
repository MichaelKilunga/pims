<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Run extends Model
{
    /** @use HasFactory<\Database\Factories\RunFactory> */
    use HasFactory;

    protected $fillable = ['started_at', 'completed_at', 'type', 'status', 'findings_count', 'meta'];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'meta' => 'array',
    ];
}
