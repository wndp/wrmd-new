<?php

namespace App\Models;

use App\Concerns\HasUniqueFields;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class ForumGroup extends Model
{
    use HasFactory;
    use HasSlug;
    use HasUniqueFields;
    use HasVersion7Uuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'settings',
    ];

    /**
     * The attributes that should be casted to native types.
     *
     * @var array
     */
    protected $casts = [
        'settings' => 'json',
    ];

    /**
     * The attributes that should be unique in the database.
     *
     * @var array<string, string>
     */
    protected array $unique = [
        'slug',
    ];

    /**
     * A forum group has many members.
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'forum_group_members', 'forum_group_id')
            ->withPivot('role', 'settings')
            ->withTimestamps();
    }

    /**
     * A forum group has many threads.
     */
    public function threads(): BelongsToMany
    {
        return $this->belongsToMany(Thread::class, 'forum_group_threads', 'forum_group_id')
            ->withTimestamps();
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * Determine if the provided team is a member of the current group.
     */
    public function hasMember(Team $team): bool
    {
        return $this->members()
            ->where('team_id', $team->id)
            ->exists();
    }
}
