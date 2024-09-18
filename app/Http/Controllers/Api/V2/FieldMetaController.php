<?php

namespace App\Http\Controllers\Api\V2;

use App\Domain\Options;
use App\Domain\Patients\Patient;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class FieldMetaController extends Controller
{
    /**
     * Store trained labels on a patient.
     *
     * @param  \App\Domain\Patients\Patient  $patient
     * @param  string  $category
     * @return void
     */
    public function __invoke(Request $request)
    {
        $request->validate([
            'field' => 'required',
        ]);

        $field = fields()->getField($request->field);

        if ($field['type'] === 'select') {
            $field['options'] = Options::arrayToSelectable($field['options']);
        }

        return $field;
    }
}
