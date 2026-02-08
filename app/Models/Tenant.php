<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'plan',
        'ai_monthly_budget_usd',
        'active',
        'settings',
    ];

    protected $casts = [
        'active' => 'boolean',
        'ai_monthly_budget_usd' => 'decimal:2',
        'settings' => 'array',
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

    /**
     * Runs owned by this tenant.
     */
    public function runs(): HasMany
    {
        return $this->hasMany(Run::class);
    }
}
