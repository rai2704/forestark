<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $fillable = ['name', 'full_access'];

    protected $casts = ['full_access' => 'boolean'];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
