<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Reply extends Model
{
    use HasFactory;
    use HasVersion7Uuids;

    protected $fillable = [
        'body',
    ];

    protected $appends = [
        'created_at_for_humans',
        'body_for_humans',
    ];

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getBodyForHumansAttribute(): string
    {
        return Str::markdown($this->body, [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ]);
    }

    public function getCreatedAtForHumansAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
