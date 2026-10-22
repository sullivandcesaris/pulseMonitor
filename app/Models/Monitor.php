<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Monitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'organization_id', 'name', 'target_url', 'method', 'interval_seconds',
        'expected_status', 'timeout_seconds', 'is_enabled', 'last_checked_at', 'next_check_at',
    ];

    protected function casts(): array
    {
        return ['is_enabled' => 'boolean', 'last_checked_at' => 'datetime', 'next_check_at' => 'datetime'];
    }

    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    public function checks(): HasMany
    {
        return $this->hasMany(MonitorCheck::class);
    }

    public function latestCheck(): HasOne
    {
        return $this->hasOne(MonitorCheck::class)->latestOfMany('checked_at');
    }

    public function incidents(): HasMany
    {
        return $this->hasMany(Incident::class);
    }
}
