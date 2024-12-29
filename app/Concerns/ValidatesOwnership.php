<?php

namespace App\Concerns;

use App\Exceptions\RecordNotOwned;
use App\Models\Admission;
use App\Models\Patient;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;

trait ValidatesOwnership
{
    /**
     * Determine if a record belongs to a team.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException
     */
    public function validateOwnership(int $teamId, ?string $message = null): self
    {
        if ($this->isOwnedBy($teamId)) {
            return $this;
        }

        throw new HttpResponseException(
            (new RecordNotOwned())->toResponse(request())
        );
    }

    public function validateRelationshipWithPatient(Patient $patient)
    {
        if ($this->patient_id === $patient->id) {
            return $this;
        }

        throw new HttpResponseException(
            (new RecordNotOwned())->toResponse(request())
        );
    }

    public function isOwnedBy(int $teamId): bool
    {
        if (method_exists($this, 'team') && $this->team_id == $teamId) {
            return true;
        }

        if ($this instanceof Patient) {
            return $this->admissions()->where('team_id', $teamId)->first() instanceof Admission;
        }

        if ($this instanceof User) {
            return $this->allTeams()->contains('id', $teamId);
        }

        return Admission::where([
            'team_id' => $teamId,
            'patient_id' => $this->patient_id,
        ])->exists();
    }
}
