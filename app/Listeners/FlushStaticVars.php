<?php

namespace App\Listeners;

use App\Listing\ListsCollection;
use App\Repositories\OptionsStore;
use App\Rules\AttributeOptionExistsRule;

class FlushStaticVars
{
    public function handle(object $event): void
    {
        OptionsStore::clearCache();
        ListsCollection::clearCache();
        AttributeOptionExistsRule::clearCache();
    }
}
