<?php

namespace App\Analytics\Maps;

use App\Analytics\Contracts\Map;
use App\Analytics\Series;
use App\Enums\AccountStatus;
use App\Models\Team;

class WrmdAccounts extends Map
{
    public function compute()
    {
        $this->series = new Series();

        $this->series->push(
            [
                'name' => AccountStatus::ACTIVE->label(),
                'data' => Team::where('status', AccountStatus::ACTIVE)
                    ->get()
                    ->map([$this, 'formatMarkers'])
                    ->filter()
                    ->values(),
            ]
        );

        $this->series->push(
            [
                'name' => AccountStatus::STALE->label(),
                'data' => Team::where('status', AccountStatus::STALE)
                    ->get()
                    ->map([$this, 'formatMarkers'])
                    ->filter()
                    ->values(),
            ]
        );

        $this->series->push(
            [
                'name' => AccountStatus::SUSPENDED->label(),
                'data' => Team::where('status', AccountStatus::SUSPENDED)
                    ->get()
                    ->map([$this, 'formatMarkers'])
                    ->filter()
                    ->values(),
            ]
        );
    }

    public function formatMarkers($account)
    {
        if ($account->coordinates) {
            return [
                'coordinates' => [
                    'lat' => $account->coordinates->latitude,
                    'lng' => $account->coordinates->longitude,
                ],
                'title' => $account->organization,
                'content' => $account->full_address,
                'link' => '',
            ];
        }
    }
}
