<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Classifications\Terminology;
use App\Http\Controllers\Controller;
use App\Jobs\DestroyStoredPredictions;
use App\Jobs\RenameStoredPredictions;
use App\Rules\Base64;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CustomTerminologyController extends Controller
{
    public static $customTerms;

    public function __construct()
    {
        $self = $this;

        Collection::macro('mapTreeList', function () use ($self) {
            return $this->map(function ($branch, $node) use ($self) {
                if (! is_array($branch)) {
                    $custom = $self::customTermByName($branch);
                    $isCustom = $custom instanceof Terminology;

                    return [
                        'id' => $isCustom ? $custom->id : base64_encode($branch),
                        'text' => $branch,
                        'data' => [
                            'canHaveCustom' => $isCustom ? false : true,
                            'isCustom' => $isCustom,
                        ],
                    ];
                }

                return [
                    'id' => base64_encode($node),
                    'text' => $node,
                    'children' => (new static($branch))->mapTreeList()->values()->toArray(),
                    'data' => [
                        'canHaveCustom' => $self::canHaveCustom($node, $branch),
                        'isCustom' => false,
                    ],
                ];
            });
        });
    }

    /**
     * Return the categories custom terms.
     */
    public function index(string $category): JsonResponse
    {
        $category = Str::studly($category);
        $termsClass = app("App\Domain\Classifications\\$category");

        static::$customTerms = $termsClass::customTerms(Auth::user()->current_team_id);

        $tree = collect($termsClass::tree())->mapTreeList()->values()->toArray();

        return response()->json($tree);
    }

    /**
     * Store a new custom term for the provided category.
     */
    public function store(string $category): JsonResponse
    {
        request()->validate([
            'parentId' => ['required', new Base64()],
            'name' => 'required',
        ]);

        $currentAccount = Auth::user()->current_account;
        $category = Str::studly($category);
        $termsClass = app("App\Domain\Classifications\\$category");
        $termsClass::withOutCustom();
        $parentNode = base64_decode(request('parentId'));
        $path = array_search($parentNode, $termsClass::dot());

        abort_unless($path, 404);

        try {
            $terminology = new Terminology([
                'category' => $category,
                'name' => request('name'),
                'path' => $this->pushReplace($path, $parentNode),
            ]);
            $terminology->account()->associate($currentAccount);
            $terminology->save();
        } catch (QueryException $e) {
            return response()->json([
                'message' => Str::contains($e->getMessage(), 'Duplicate entry')
                    ? 'A term with the name "'.request('name').'" already exits for the category '.Str::title(Str::snake($category, ' ')).'.'
                    : 'An unknown error occurred.',
            ], 409);
        }

        $termsClass::forgetCustomTerms($currentAccount->id);

        return response()->json($terminology);
    }

    /**
     * Update a custom term.
     */
    public function update(string $category, Terminology $terminology): JsonResponse
    {
        request()->validate([
            'name' => 'required',
        ]);

        $currentAccount = Auth::user()->current_account;
        $category = Str::studly($category);
        $oldName = $terminology->name;

        try {
            $terminology
                ->validateOwnership($currentAccount->id)
                ->update(['name' => request('name')]);
        } catch (QueryException $e) {
            return response()->json([
                'status' => 'failed',
                'message' => Str::contains($e->getMessage(), 'Duplicate entry')
                    ? 'A term with the name "'.request('name').'" already exits for the category '.Str::title(Str::snake($category, ' ')).'.'
                    : 'An unknown error occurred.',
            ], 409);
        }

        app("App\Domain\Classifications\\$category")::forgetCustomTerms($currentAccount->id);

        RenameStoredPredictions::dispatch($currentAccount, $category, $oldName, request('name'));

        return response()->json([
            'status' => 'success',
            'message' => '',
        ]);
    }

    /**
     * Delete a custom term.
     */
    public function destroy(string $category, Terminology $terminology): JsonResponse
    {
        $currentAccount = Auth::user()->current_account;
        $category = Str::studly($category);
        $name = $terminology->name;

        $result = $terminology
            ->validateOwnership($currentAccount->id)
            ->delete();

        app("App\Domain\Classifications\\$category")::forgetCustomTerms($currentAccount->id);

        DestroyStoredPredictions::dispatch($currentAccount, $category, $name);

        return response()->json($result);
    }

    /**
     * Replace the last node in a "dot" notation string.
     */
    private function pushReplace(string $path, string $node): string
    {
        $path = explode('.', $path);

        array_pop($path);
        array_push($path, $node);

        return implode('.', $path);
    }

    /**
     * Determine of the term can have custom terms added underneath it.
     *
     * @return bool
     */
    public static function canHaveCustom(string $term, array $children)
    {
        /*
         * If the term's children is an array the then term is a root node.
         * If the root node's children is empty then allow for custom terms.
         */
        if (is_array($children) && count($children) === 0) {
            return true;
        }

        /*
         * Determine if the term has custom terms within its own path and allow
         * more custom terms if so.
         */
        return static::$customTerms->contains(function ($customTerm) use ($term) {
            return Arr::last(explode('.', $customTerm->path)) === $term;
        });
    }

    /**
     * Get the first custom term by its name.
     */
    public static function customTermByName(string $term)
    {
        return static::$customTerms->firstWhere('name', $term);
    }
}
