<?php

use App\Models\Patient;
use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\Facades\Cookie;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('team.{teamId}', function ($user, $teamId) {
    return $user->belongsToTeam(Team::find($teamId));
});

Broadcast::channel('device.{uuid}', function ($user, $uuid) {
    return Cookie::get('device-uuid') === $uuid;
});

Broadcast::channel('patient.{patientId}', function ($user, $patientId) {
    return Patient::find($patientId)->isOwnedBy($user->current_account_id);
});
