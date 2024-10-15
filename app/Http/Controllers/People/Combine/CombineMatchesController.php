<?php

namespace App\Http\Controllers\People\Combine;

use App\Enums\Attribute;
use App\Http\Controllers\Controller;
use App\Models\Person;
use Illuminate\Database\Query\Expression;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CombineMatchesController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $request->validate([
            'fields' => 'required|array',
        ], [
            'fields.required' => __('At least one field must be selected to search.'),
        ]);

        $query = Person::addSelect($this->concatPeople($request->fields))
            ->addSelect(DB::raw('min(id) as id'))
            ->addSelect(DB::raw('count(*) as aggregate'))
            ->where('team_id', Auth::user()->current_team_id)
            ->groupBy('match')
            ->having('aggregate', '>', 1)
            ->orderByDesc('aggregate')
            ->with('patients');

        foreach ($request->fields as $field) {
            $query->whereNotNull($field)->where($field, '!=', '');
        }

        $people = $query->cursor();
        $sentence = $this->sentenceize($request->fields);

        return Inertia::render('People/Combine/Matches', compact('people', 'sentence'));
    }

    /**
     * Concatenate fields together into a string.
     */
    private function concatPeople(array $fields): Expression
    {
        $string = 'trim('.implode('), trim(', array_map('protect_identifiers', $fields)).')';

        return DB::raw("concat_ws(' ', $string) as `match`");
    }

    /**
     * Sentence-ize an array of fields into a readable sentence.
     */
    private function sentenceize(array $fields): string
    {
        $fields = Arr::prefix($fields, 'people.');

        return Collection::make($fields)
            ->transform(fn ($field) => Attribute::tryFrom($field)->label())
            ->join(', ', ' and ');

        // foreach ($fields as $field) {
        //     Attribute::tryFrom($field)
        // }

        // // Attribute
        // return fields()
        //     ->getLabels()
        //     ->byTable('people')
        //     ->mapWithKeys(
        //         fn ($label, $column) => [Str::after($column, 'people.') => $label]
        //     )
        //     ->intersectByKeys(array_flip($fields))
        //     ->values()
        //     ->transform(fn ($label) => Str::of($label)->after('Rescuer ')->lower())
        //     ->join(', ', ' and ');
    }
}
