<?php

namespace App\Http\Controllers\Admin;

use App\Domain\Admissions\Admission;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class MisidentifiedController extends Controller
{
    /**
     * Display the admin misidentified patient view.
     */
    public function __invoke(Request $request): Response
    {
        $misidentifiedPatients = Admission::select('admissions.*')
            ->whereMisidentified()
            ->when($request->input('common_name'), function ($query, $commonName) {
                $query->where('common_name', 'like', "%{$commonName}%");
            })
            ->orderBy('common_name')
            ->with('patient', 'account')
            ->paginate()
            ->onEachSide(1);

        return Inertia::render('Admin/Taxa/Misidentified', compact('misidentifiedPatients'));
    }
}