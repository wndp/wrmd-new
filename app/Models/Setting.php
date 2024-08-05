<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * The attributes that are revisionable.
     *
     * @var array
     */
    protected $revisionable = [
        'key',
        'value',
    ];

    /**
     * The list of attributes to cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'array',
    ];

    /**
     * Every setting belongs to an account.
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
