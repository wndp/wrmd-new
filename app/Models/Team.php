<?php

namespace App\Models;

use App\Concerns\HasSubAccounts;
use App\Enums\AccountStatus;
use App\Repositories\SettingsStore;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    use HasFactory;
    use HasProfilePhoto;
    use HasSubAccounts;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
            'status' => AccountStatus::class
        ];
    }

    /**
     * An account has many extensions.
     */
    public function extensions(): BelongsToMany
    {
        return $this->belongsToMany(Extension::class, 'team_extension', 'team_id', 'extension_id')->withTimestamps();
    }

    /**
     * Get the accounts settings.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    /**
     * Return the accounts SettingsStore.
     */
    public function settingsStore(): SettingsStore
    {
        return new SettingsStore($this);
    }
}
