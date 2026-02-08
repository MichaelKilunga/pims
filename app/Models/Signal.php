<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Domain;

class Signal extends Model
{
    /** @use HasFactory<\Database\Factories\SignalFactory> */
    use HasFactory, HasTenant;

    protected $fillable = [
        'tenant_id',
        'domain_id',
        'source_id',
        'title',
        'url',
        'fingerprint',
        'relevance_score',
        'summary',
        'implications',
        'action_required',
        'qualified_for_analysis',
        'published_at',
        'meta',
    ];

    protected $casts = [
        'relevance_score' => 'decimal:2',
        'action_required' => 'integer',
        'qualified_for_analysis' => 'boolean',
        'published_at' => 'datetime',
        'meta' => 'array',
    ];

    public function domain(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Domain::class);
    }

    public function source(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Source::class);
    }
}
