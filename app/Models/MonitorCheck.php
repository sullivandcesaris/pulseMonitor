<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MonitorCheck extends Model
{
    use HasFactory;

    protected $fillable = ['monitor_id', 'status', 'http_status', 'response_time_ms', 'error_message', 'checked_at'];

    protected function casts(): array
    {
        return ['checked_at' => 'datetime'];
    }

    public function monitor(): BelongsTo
    {
        return $this->belongsTo(Monitor::class);
    }
}
