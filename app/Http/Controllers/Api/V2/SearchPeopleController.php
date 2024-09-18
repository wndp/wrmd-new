<?php

namespace App\Http\Controllers\Api\V2;

use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class SearchPeopleController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $query = Person::where('team_id', Auth::user()->current_team_id);

        $attributes = array_merge(
            $query->getModel()->getFillable(),
            array_keys($query->getModel()->attributesToArray())
        );

        collect($request->all($attributes))
            ->filter()
            ->whenEmpty(fn () => abort(Response::HTTP_NO_CONTENT))
            ->each(function ($value, $key) use ($query, $request) {
                if ($key === 'full_name') {
                    [$first, $last] = array_pad(explode(' ', $value), 2, null);

                    if (empty($last)) {
                        $query->where(function ($query) use ($first) {
                            $query->where('first_name', 'like', "%$first%")->orWhere('last_name', 'like', "%$first%");
                        });
                    } else {
                        $query->where('first_name', 'like', "%$first%")->where('last_name', 'like', "%$last%");
                    }
                } elseif ($key === 'phone') {
                    $query->where(function ($q) use ($value) {
                        $phone = sanatize_phone($value);
                        $q->where('phone', 'like', "%$phone%")->orWhere('alt_phone', 'like', "%$phone%");
                    });
                } elseif (in_array($key, ['is_volunteer', 'is_member', 'no_solicitations'])) {
                    $query->where($key, $request->boolean($key));
                } else {
                    $query->where($key, 'like', "%$value%");
                }
            });

        return response()->json(
            $query->get()
        );
    }
}
