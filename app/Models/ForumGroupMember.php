<?php

namespace App\Models;

use App\Enums\ForumGroupRole;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ForumGroupMember extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'role',
        'settings',
    ];

    protected $casts = [
        'role' => ForumGroupRole::class,
        'settings' => 'json',
    ];

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ForumGroup::class);
    }
}
