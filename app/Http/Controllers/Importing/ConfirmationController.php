<?php

namespace App\Http\Controllers\Importing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ConfirmationController extends Controller
{
    //use FacilitatesImporting;

    /**
     * Display the view for confirming the file declarations.
     */
    public function index(): Response
    {
        $dateAttributes = array_intersect_key(session('import')['mappedHeadings'], fields('us')->byType('date')->all());

        return Inertia::render('Maintenance/Importing/Confirmation', compact('dateAttributes'));
    }
}
