<?php

use App\Http\Controllers\Search\AdvancedSearchController;
use App\Http\Controllers\Search\CommonNamesPrefetchController;
use App\Http\Controllers\Search\QuickSearchController;
use App\Http\Controllers\Search\SearchCommonNamesController;
use App\Http\Controllers\Search\SearchController;

Route::name('search.')->group(function () {
    Route::post('search/quick', QuickSearchController::class)->name('quick');

    Route::get('search/{tab?}', [SearchController::class, 'create'])->name('simple.create');
    Route::post('search', [SearchController::class, 'search'])->name('simple.search');

    Route::get('search-advanced', [AdvancedSearchController::class, 'create'])->name('advanced.create');
    Route::post('search-advanced', [AdvancedSearchController::class, 'search'])->name('advanced.search');

    Route::get('internal-api/search/common-names-prefetch', CommonNamesPrefetchController::class)->name('common-names-prefetch');
    Route::get('internal-api/search/common-names', SearchCommonNamesController::class)->name('common-names');
});
