<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Run extends Model
{
    /** @use HasFactory<\Database\Factories\RunFactory> */
    use HasFactory, HasTenant;

    protected $fillable = ['tenant_id', 'started_at', 'completed_at', 'type', 'status', 'findings_count', 'meta'];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'meta' => 'array',
    ];
}
