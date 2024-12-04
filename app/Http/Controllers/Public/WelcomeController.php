<?php

namespace App\Http\Controllers\Public;

use App\Repositories\TeamRepository;
use App\Models\Testimonial;
use App\Http\Controllers\Controller;
use App\Repositories\RecentNews;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class WelcomeController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        $accounts = TeamRepository::active();
        $whatsNew = RecentNews::collect()->first();
        $organizations = $accounts->count();
        $countries = $accounts->unique('country')->pluck('country')->count();
        $usStates = $accounts->where('country', 'US')->unique('subdivision')->count();
        $testimonials = Testimonial::with('team')->inRandomOrder()->limit(2)->get()->pad(2, ['team' => (object) []]);
        $avatars = $accounts->filter(fn ($team) => $team->profile_photo_url)->shuffle()->take(6);

        $analyticFiltersForThisYear = [
            'segments' => ['All Patients'],
            'date_from' => Carbon::now()->startOfYear()->format('Y-m-d'),
            'date_to' => Carbon::now()->format('Y-m-d'),
            'group_by_period' => 'day',
        ];

        return Inertia::render('Welcome', compact(
            'analyticFiltersForThisYear',
            'whatsNew',
            'organizations',
            'countries',
            'usStates',
            'testimonials',
            'avatars',
        ));
    }
}
