<?php

namespace App\Models;

use App\Concerns\AssistWithRolesAndAbilities;
use App\Concerns\HasUniqueFields;
use App\Concerns\ValidatesOwnership;
use App\Enums\Role;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Silber\Bouncer\BouncerFacade;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable implements MustVerifyEmail
{
    use AssistWithRolesAndAbilities;
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasRolesAndAbilities;
    use HasTeams;
    use HasUniqueFields;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use ValidatesOwnership;
    use LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'is_api_user',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_api_user' => 'boolean',
        ];
    }

    /**
     * The attributes that should be unique in the database.
     *
     * @var array<string, string>
     */
    protected array $unique = [
        'email',
    ];

    public function teammates()
    {
        return $this->currentTeam
            ->allUsers()
            ->where('user_id', '!=', $this->id);
    }

    public function joinTeam(Team $team, Role $role)
    {
        $team->users()->attach($this);

        BouncerFacade::scope()->to($team->id)->onlyRelations()->dontScopeRoleAbilities();
        BouncerFacade::assign($role->value)->to($this->id);

        return $this;
    }

    /**
     * Return a team's API user.
     */
    public static function apiUserFor(Team $team): User
    {
        $user = static::firstOrCreate([
            'email' => "api-user-{$team->id}@wrmd.org",
            'is_api_user' => true,
        ], [
            'name' => "API User For {$team->name}",
            'password' => Hash::make(Str::random()),
        ]);

        $team->users()->syncWithoutDetaching($user);

        return $user;
    }

    public static function wrmdbot(): User
    {
        return static::firstOrCreate([
            'email' => 'support@wildneighborsdp.org',
        ], [
            'name' => 'Wrmdbot',
        ]);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
