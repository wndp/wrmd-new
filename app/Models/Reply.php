<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reply extends Model
{
    use HasFactory;

    protected $fillable = [
        'body',
    ];

    protected $appends = [
        'created_at_for_humans',
        'body_for_humans',
    ];

    /**
     * A reply belongs to a team.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * A reply is written by a user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Return an HTML formatted version of the body.
     */
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
