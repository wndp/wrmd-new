<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasVersion7Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transfer extends Model
{
    use HasFactory;
    use HasVersion7Uuids;

    protected $fillable = [
        'patient_id',
        'cloned_patient_id',
        'from_team_id',
        'to_team_id',
        //'thread_id',
        'is_collaborative',
        'is_accepted',
        'responded_at',
    ];

    protected $casts = [
        'uuid' => 'string',
        'patient_id' => 'integer',
        'cloned_patient_id' => 'integer',
        'from_team_id' => 'integer',
        'to_team_id' => 'integer',
        //'thread_id' => 'integer',
        'is_collaborative' => 'boolean',
        'is_accepted' => 'boolean',
        'responded_at' => 'datetime',
    ];

    public function patient(): BelongsTo
    {
        return $this->belongsTo(Patient::class);
    }

    public function clonedPatient(): BelongsTo
    {
        return $this->belongsTo(Patient::class, 'cloned_patient_id');
    }

    public function fromTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'from_team_id');
    }

    public function toTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'to_team_id');
    }

    public function thread(): BelongsTo
    {
        return $this->belongsTo(Thread::class);
    }

    public function fromTeamAdmission()
    {
        return Admission::custody($this->fromTeam, $this->patient);
    }

    /**
     * Mark a transfer request as accepted.
     */
    public function accept(): void
    {
        $this->update([
            'is_accepted' => true,
            'responded_at' => Carbon::now(),
        ]);
    }

    /**
     * Mark a transfer request as denied.
     */
    public function deny(): void
    {
        $this->update([
            'is_accepted' => false,
            'responded_at' => Carbon::now(),
        ]);
    }
}
