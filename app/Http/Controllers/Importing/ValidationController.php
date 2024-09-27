<?php

namespace App\Http\Controllers\Importing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ValidationController extends Controller
{
    //use FacilitatesImporting;

    /**
     * Validate the data for a requested rule.
     */
    public function index(): JsonResponse
    {
        $data = request()->validate([
            'rule' => 'required',
            'attribute' => 'required',
        ]);

        $validator = 'App\Domain\Importing\Validators\Validate'.ucfirst($data['rule']);
        $validator = new $validator();

        return response()->json([
            'success' => $validator->validate(
                $this->getImportCollectionForSession(session('import.sessionId'), session('import.filePath')),
                $data['attribute']
            ),
            'data' => $validator->getFailed(),
        ]);
    }
}
