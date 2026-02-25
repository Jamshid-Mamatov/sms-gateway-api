<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'provider_id',
        'api_key',
        "is_active"
    ];

    protected $casts = [
        'is_active'=>"boolean"
    ];

    protected static function booted(){
        static::creating(function(Project $project){
            if(empty($project->api_key)){
                $project->api_key=self::generateApikey();
            }
        });
    }

    public static function generateApikey():string{
        do {
            $key = 'sk_' . Str::random(60);
        } while (self::where('api_key', $key)->exists());
        return $key;
    }

    public function provider():BelongsTo{
        return $this->belongsTo(Provider::class);
    }

    public function smsMessages():HasMany{
        return $this->hasMany(SmsMessage::class);
    }
}
