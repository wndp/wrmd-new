<?php

use App\Http\Controllers\Public\ContactController;
use App\Http\Controllers\Public\DonateController;
use App\Http\Controllers\Public\PublicController;
use App\Http\Controllers\Public\WelcomeController;
use Spatie\Honeypot\ProtectAgainstSpam;

Route::get('/', WelcomeController::class)->name('home');
Route::inertia('style-guide', 'StyleGuide');
Route::inertia('about', 'Public/About')->name('about');
Route::inertia('the-wild-neighbors-database-project', 'Public/Wndp')->name('about.wndp');
Route::inertia('security-and-data-integrity', 'Public/Security')->name('about.security');
Route::inertia('support-us', 'Public/SupportUs')->name('about.support-us');
Route::inertia('wildalert', 'Public/WildAlert')->name('about.wildalert');
Route::inertia('oil-spills', 'Public/OilSpills')->name('about.oil-spills');
Route::inertia('pricing', 'Public/Pricing')->name('about.pricing');
Route::inertia('terms-and-conditions', 'Public/Terms')->name('about.terms');
Route::inertia('privacy-policy', 'Public/Privacy')->name('about.privacy');
Route::inertia('inactive-account-policy', 'Public/InactiveAccount')->name('about.inactive-account');
Route::inertia('sla', 'Public/Sla')->name('about.sla');
Route::controller(DonateController::class)->group(function () {
    Route::get('donate', 'index')->name('donate.index');
    Route::get('thank-you', 'thanks')->name('donate.thanks');
});
Route::controller(ContactController::class)->group(function () {
    Route::get('contact', 'create')->name('contact.create');
    Route::post('contact', 'store')->middleware(ProtectAgainstSpam::class)->name('contact.store');
});
Route::controller(PublicController::class)->group(function () {
    Route::get('testimonials', 'testimonials')->name('about.testimonials');
    Route::get('features', 'features')->name('about.features');
    Route::get('importing', 'importing')->name('about.importing');
    Route::get('whats-new', 'new')->name('about.new');
    Route::get('agencies', 'agencies')->name('about.agencies');
});
