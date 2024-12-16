<?php

namespace App\Http\Controllers;

use App\Enums\Entity;
use App\Models\Incident;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class RelationshipsController extends Controller
{
    public function __construct()
    {
        Validator::extend(
            'model_exists',
            function ($attribute, $value, $parameters, $validator) {
                return ! is_null(Wrmd::resourceForModel($value));

                return class_exists($value) and is_a(new $value, Model::class);
            }
        );

        Validator::replacer(
            'model_exists',
            function ($message, $attribute, $rule, $parameters) {
                $attribute = str_replace('_', ' ', Str::snake($attribute));

                return "The $attribute field is not a model.";
            }
        );
    }

    /**
     * Return a list or potential model relationships.
     */
    public function index(Request $request, string $model): JsonResponse
    {
        // $incidents = Incident::where('account_id', Auth::user()->current_team_id)
        //     ->where('incident_number', 'like', $request->query('search').'%')
        //     ->get()
        //     ->transform(fn ($incident) => [
        //         'id' => $incident->id,
        //         'resource' => 'incident',
        //         'heading' => $incident->incident_number,
        //         'sub_heading' => $incident->suspected_species,
        //         'date' => $incident->reported_at->toFormattedDateString(),
        //     ]);

        // return response()->json($incidents);
    }

    /**
     * Store a model to model relationship.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'source_type' => 'required|model_exists',
            'source_id' => 'required|numeric',
            'related_type' => 'required|model_exists',
            'related_id' => 'required|numeric',
        ]);

        $sourceModel = $request->enum('source_type', Entity::class)
            ->owningModelInstance($request->integer('source_id'))
            ->validateOwnership(Auth::user()->current_team_id);

        $relatedModel = $request->enum('related_type', Entity::class)
            ->owningModelInstance($request->integer('related_id'))
            ->validateOwnership(Auth::user()->current_team_id);

        $sourceModel->relate($relatedModel);
    }

    /**
     * Show the relationships for the provided model.
     */
    public function show(Request $request): JsonResponse
    {
        $data = $request->validate([
            'model_type' => 'required|model_exists',
            'model_id' => 'required|numeric',
        ]);

        $model = $request->enum('model_type', Entity::class)
            ->owningModelInstance($request->integer('model_id'))
            ->validateOwnership(Auth::user()->current_team_id);

        // $model = Wrmd::resourceForModelOrFail($data['model_type'], $data['model_id'])
        //     ->validateOwnership(Auth::user()->current_team_id);

        return response()->json($model->related);
    }

    /**
     * Try to load and validate a model by it's ID.
     */
    // protected function loadModel(sting $model, int $id): Model
    // {
    //     Wrmd::resourceForModelOrFail($model, $id)->validateOwnership(Auth::user()->current_team_id);

    //     try {
    //         return (new $model())::findOrFail($id)->validateOwnership(Auth::user()->current_team_id);
    //     } catch (ModelNotFoundException $e) {
    //         abort(422, "No query results for model {$model}");
    //     }
    // }
}
