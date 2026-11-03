<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Language extends Model
{
    protected $fillable = ['code', 'name', 'is_default', 'is_active', 'position'];

    protected $casts = ['is_default' => 'boolean', 'is_active' => 'boolean'];
}
