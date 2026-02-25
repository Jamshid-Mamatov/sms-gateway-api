<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SmsMessage extends Model
{
    protected $fillable = [
        'project_id',
        'phone',
        'message',
        'status',
        'provider_response',
        'provider_message_id',
        'sent_at'
    ];

    protected $casts = [
        'provider_response' => 'array',
        'sent_at'           => 'datetime',
    ];

    public function project():BelongsTo{
        return $this->belongsTo(Project::class);
    }

    public function scopeByStatus(Builder $query, $status): Builder
    {
        return $status ? $query->where('status', $status) : $query;
    }

    public function scopeByPhone(Builder $query, $phone): Builder
    {
        return $phone ? $query->where('phone', 'like', "%{$phone}%") : $query;
    }

    public function scopeByDateRange(Builder $query, ?string $from, ?string $to): Builder
    {
        if ($from) {
            $query->whereDate('created_at', '>=', $from);
        }
        if ($to) {
            $query->whereDate('created_at', '<=', $to);
        }
        return $query;
    }
}
