<?php

namespace App\Models;

use App\Support\Content;
use Illuminate\Database\Eloquent\Model;

class Newsletter extends Model
{
    protected $fillable = ['data', 'sent_at', 'sent_count', 'failed_count'];

    protected $casts = ['data' => 'array', 'sent_at' => 'datetime'];

    public function content(?string $locale = null): array
    {
        return Content::resolve(config('portal.newsletter_fields'), $this->data ?? [], $locale);
    }
}
