<?php

namespace App\Listeners;

use App\Listing\ListsCollection;
use App\Repositories\OptionsStore;
use App\Rules\AttributeOptionExistsRule;
use App\Support\ExtensionNavigation;

class FlushStaticVars
{
    public function handle(object $event): void
    {
        OptionsStore::clearCache();
        ListsCollection::clearCache();
        ExtensionNavigation::clearCache();
        AttributeOptionExistsRule::clearCache();
    }
}
