<?php

namespace App\Models;

use App\Concerns\AssistsWithTeams;
use App\Concerns\AssistWithRolesAndAbilities;
use App\Concerns\HasNoPersonalTeam;
use App\Concerns\HasUniqueFields;
use App\Concerns\ValidatesOwnership;
use Carbon\Carbon;
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
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable implements MustVerifyEmail
{
    use AssistsWithTeams;
    use AssistWithRolesAndAbilities;
    use HasApiTokens;
    use HasFactory;
    use HasNoPersonalTeam, HasTeams {
        HasNoPersonalTeam::ownsTeam insteadof HasTeams;
        HasNoPersonalTeam::isCurrentTeam insteadof HasTeams;
    }
    use HasProfilePhoto;
    use HasRolesAndAbilities;
    use HasUniqueFields;
    use LogsActivity;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use ValidatesOwnership;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_api_user',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_api_user' => 'boolean',
        ];
    }

    protected array $unique = [
        'email',
    ];

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
            'name' => 'WRMD Bot',
            'password' => Hash::make(Str::random()),
            'email_verified_at' => Carbon::now(),
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
