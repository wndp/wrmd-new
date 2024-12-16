<?php

namespace App\Http\Controllers\Importing;

use App\Http\Controllers\Controller;

class TranslationController extends Controller
{
    //use FacilitatesImporting;

    /**
     * Display the form for translating file values.
     */
    public function index(): Response
    {
        $translatable = fields()->byType(['select', 'boolean'])->keys()->flip()->all();
        $translatableHeadings = array_intersect_key(session('import.mappedHeadings'), $translatable);
        $worksheet = $this->getImportCollectionForSession(session('import.sessionId'), session('import.filePath'));

        $foreignValues = [];
        foreach ($translatableHeadings as $column => $heading) {
            foreach ($worksheet as $row) {
                $foreignValues[$column][] = $row->get($heading);
            }

            $foreignValues[$column] = array_unique($foreignValues[$column]);
        }

        return Inertia::render('Maintenance/Importing/Translation', compact('foreignValues'));
    }

    /**
     * Store a lookup map of the file values in memory.
     */
    public function store(): RedirectResponse
    {
        request()->validate([
            'translated_values' => 'nullable|array',
        ]);

        session()->put('import.translatedValues', request('translated_values', []));

        return redirect()->route('import.confirmation.index');
    }
}
