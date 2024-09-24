<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ThreadSubscription extends Pivot
{
    use HasVersion7Uuids;

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }
}
