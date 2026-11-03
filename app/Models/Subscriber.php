<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Subscriber extends Model
{
    protected $fillable = ['email', 'locale', 'confirmed_at'];

    protected $casts = ['confirmed_at' => 'datetime'];

    protected static function booted(): void
    {
        static::creating(fn (self $s) => $s->token ??= Str::random(64));
    }

    public function scopeConfirmed($query)
    {
        return $query->whereNotNull('confirmed_at');
    }
}
