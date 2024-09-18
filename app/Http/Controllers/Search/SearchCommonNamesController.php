<?php

namespace App\Http\Controllers\Search;

use App\Domain\Taxonomy\CommonName;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchCommonNamesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): JsonResponse
    {
        return response()->json(
            $request->filled('q') ? CommonName::search($request->input('q'))->get() : []
        );
    }
}
