<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Patients\Patient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TrainingController extends Controller
{
    /**
     * Store trained labels on a patient.
     *
     * @return void
     */
    public function store(Request $request, Patient $patient, string $category)
    {
        $data = $request->validate([
            'text.*' => 'required',
            'isSuspected.*' => 'nullable|boolean',
        ]);

        $patient->predictions()->where('category', Str::studly($category))->delete();

        collect($request->all())->map(function ($value) use ($category) {
            return [
                'category' => Str::studly($category),
                'prediction' => $value['text'],
                'is_suspected' => $value['isSuspected'] ?? false,
                'is_validated' => $value['isValidated'] ?? false,
                'accuracy' => 1,
            ];
        })
            ->each(function ($prediction) use ($patient) {
                $patient->predictions()->updateOrCreate($prediction);
            });
    }
}
