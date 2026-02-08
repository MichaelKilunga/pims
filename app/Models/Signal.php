<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Domain;

class Signal extends Model
{
    /** @use HasFactory<\Database\Factories\SignalFactory> */
    use HasFactory;

    protected $fillable = [
        'domain_id',
        'source_id',
        'title',
        'url',
        'fingerprint',
        'relevance_score',
        'summary',
        'implications',
        'action_required',
        'published_at',
    ];

    protected $casts = [
        'relevance_score' => 'decimal:2',
        'action_required' => 'integer',
        'published_at' => 'datetime',
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
