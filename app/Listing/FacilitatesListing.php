<?php

namespace App\Listing;

use App\Listing\LiveList;
use Illuminate\Support\Arr;

trait FacilitatesListing
{
    public function guessListFromKey($key)
    {
        $guessedList = null;

        ListsCollection::register()->each(function ($listGroup) use ($key, &$guessedList) {
            return array_filter(Arr::flatten($listGroup->lists), function ($list) use ($key, &$guessedList) {
                if ($list instanceof LiveList && $list->key() === mb_strtolower($key)) {
                    $guessedList = $list;
                }
            });
        });

        return $guessedList;
    }
}
