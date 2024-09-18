<?php

namespace App\Listing;

use App\Events\ListsRegistering;
use App\Listing\Lists\AdmittedList;
use App\Listing\Lists\AllPatientsList;
use App\Listing\Lists\ForgottenList;
use App\Listing\Lists\HomelessList;
use App\Listing\Lists\PatientsList;
use App\Listing\Lists\PatientsThisYearList;
use App\Listing\Lists\PendingPatientsList;
use App\Listing\Lists\RescuerPatientsList;
use App\Listing\Lists\SearchResultsList;
use App\Listing\Lists\SelectedPatientsList;
use App\Listing\Lists\UnderWeightList;
use App\Listing\Lists\UpdatedList;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ListsCollection extends Collection
{
    public static $lists = null;

    public function clearCache()
    {
        static::$lists = null;
    }

    public static function register(): static
    {
        if (!empty(static::$lists)) {
            return static::$lists;
        }

        $team = Auth::user()->currentTeam;

        $collection = new static();

        $collection->push(new ListGroup('Standard', [
            new PatientsThisYearList($team),
            new AllPatientsList($team),
            new PendingPatientsList($team),
            new AdmittedList($team),
            new UpdatedList($team),
            new ForgottenList($team),
            new HomelessList($team),
            new UnderWeightList($team),
        ]));

        $collection->push((new ListGroup('Hidden', [
            new SelectedPatientsList($team),
            new RescuerPatientsList($team),
            new PatientsList($team),
            new SearchResultsList($team),
        ]))->setVisibility(false));

        collect((array) event(new ListsRegistering($team)))
            ->filter()
            ->each(function ($group) use ($collection) {
                $collection->push($group);
            });

        return static::$lists = $collection;
    }
}
