<?php

namespace App\Http\Controllers\Importing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ImportController extends Controller
{
    //use FacilitatesImporting;

    /**
     * Store the import setting values in memory and trigger the import queue.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->merge(session('import'));

        $this->validate($request, [
            'filePath' => 'required',
            'fileHeadings' => 'required|array',
            'whatImporting' => 'required',
            'mappedHeadings' => 'required|array',
            'translatedValues' => 'present|array',
            'validationPassed' => 'required|in:1',
        ], [
            'filePath.required' => 'The uploaded file is no longer available. Please start the import process over.',
            'fileHeadings.required' => 'The file headings are no longer available. Please start the import process over.',
            'mappedHeadings.required' => 'The mapped file headings are no longer available. Please start the import process over.',
            'validationPassed.required' => 'The spreadsheets data did not pass validation. Please start the import process over.',
            'validationPassed.in' => 'The spreadsheets data did not pass validation. Please start the import process over.',
        ]);

        // We fire a HandleImport job so the spreadsheet file will be copied to the file system on the worker machine and not on the web/app machine.
        dispatch(new HandleImport(
            $this->loadImporter(),
            auth()->user(),
            session('import.filePath')
        ));

        return redirect()->route('import.recap.index');
    }

    /**
     * Load the correct importer object for the import session.
     */
    protected function loadImporter(): Importer
    {
        $extension = event('import.importer.'.session('import.whatImporting'));
        $importer = array_shift($extension) ?: 'App\Domain\Importing\Importers\\'.Str::studly(session('import.whatImporting')).'Importer';

        return new $importer(
            auth()->user(),
            auth()->user()->current_account,
            new Declarations(session('import'))
        );
    }
}
