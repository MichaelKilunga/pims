<?php

namespace App\Models;

use App\Traits\HasTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Domain extends Model
{
    /** @use HasFactory<\Database\Factories\DomainFactory> */
    use HasFactory, HasTenant;

    protected $fillable = ['name', 'priority', 'tenant_id'];

    public function sources(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Source::class);
    }

    public function signals(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Signal::class);
    }
}
