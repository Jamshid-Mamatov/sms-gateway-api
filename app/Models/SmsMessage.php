<?php

namespace App\Models;

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
}
