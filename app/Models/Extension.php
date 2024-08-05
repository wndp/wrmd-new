<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'tagline',
        'icon',
        'knowledge_base_id',
        'namespace',
        'is_private',
        'dependents',
    ];

    /**
     * The list of attributes to cast.
     *
     * @var array
     */
    protected $casts = [
        'is_private' => 'boolean',
        'dependents' => 'array',
    ];
}
