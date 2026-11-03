<?php

namespace App\Models;

use App\Support\Content;
use Illuminate\Database\Eloquent\Model;

class MenuItem extends Model
{
    protected $fillable = ['location', 'data', 'position', 'is_visible'];

    protected $casts = ['data' => 'array', 'is_visible' => 'boolean'];

    public function content(): array
    {
        return Content::resolve(config('portal.menu_fields'), $this->data ?? []);
    }
}
