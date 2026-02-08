<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'plan',
        'ai_monthly_budget_usd',
        'active',
        'settings',
        'owner_id',
        'setup_completed_at',
    ];

    protected $casts = [
        'active' => 'boolean',
        'ai_monthly_budget_usd' => 'decimal:2',
        'settings' => 'array',
        'setup_completed_at' => 'datetime',
    ];

    /**
     * Domains owned by this tenant.
     */
    public function domains(): HasMany
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * Sources owned by this tenant.
     */
    public function sources(): HasMany
    {
        return $this->hasMany(Source::class);
    }

    /**
     * Signals owned by this tenant.
     */
    public function signals(): HasMany
    {
        return $this->hasMany(Signal::class);
    }

    public function runs(): HasMany
    {
        return $this->hasMany(Run::class);
    }

    /**
     * The user who owns this tenant.
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
