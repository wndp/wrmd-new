<?php

namespace App\Analytics\Maps;

use App\Domain\Accounts\Account as AccountModel;
use App\Analytics\Contracts\Map;
use App\Analytics\Series;

class Account extends Map
{
    public function compute()
    {
        $account = AccountModel::find($this->team->id);

        if ($account->coordinates) {
            $this->series = (new Series())->push(
                [
                    //'name' => 'test',
                    'data' => [$this->formatMarkers($account)],
                ]
            );
        }
    }

    public function formatMarkers($account)
    {
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
