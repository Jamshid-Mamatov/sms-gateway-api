<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Provider extends Model
{
    protected $fillable = [
        'name',
        'driver',
        'config',
        "is_active"
    ];

    protected $casts = [
        'config' => 'array',
        'is_active'=>"boolean"
    ];

    protected $hidden = [
        'config'
    ];

    public function projects():HasMany
    {
        return $this->hasMany(Project::class);
    }
}
