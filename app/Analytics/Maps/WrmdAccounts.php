<?php

namespace App\Analytics\Maps;

use App\Domain\Accounts\Account;
use App\Analytics\Contracts\Map;
use App\Analytics\Series;

class WrmdAccounts extends Map
{
    public function compute()
    {
        $this->series = new Series();

        $this->series->push(
            [
                'name' => 'Active Accounts',
                'data' => Account::where('is_active', true)
                    ->get()
                    ->map([$this, 'formatMarkers'])
                    ->filter()
                    ->values(),
            ]
        );

        $this->series->push(
            [
                'name' => 'Inactive Accounts',
                'data' => Account::where('is_active', false)
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
