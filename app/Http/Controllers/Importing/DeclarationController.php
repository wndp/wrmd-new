<?php

namespace App\Http\Controllers\Importing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeclarationController extends Controller
{
    //use FacilitatesImporting;

    /**
     * Display the import declaration view.
     */
    public function index(): Response
    {
        session()->forget('import');
        session()->put(['import.sessionId' => Str::uuid()]);

        return Inertia::render('Maintenance/Importing/Declaration', [
            'whatImporting' => Options::arrayToSelectable($this->importableEntities()->toArray()),
        ]);
    }

    /**
     * Save an uploaded csv file in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'file' => 'required|file|mimes:csv,txt,xlsx,xls',
            'what_importing' => ['required', Rule::in($this->importableEntities()->keys())],
            'update_existing_records' => 'nullable|boolean',
        ]);

        $path = $data['file']->store(
            Auth::user()->current_team_id.'/import/'.session()->get('import.sessionId'),
            's3-accounts'
        );

        session()->put('import.filePath', $path);
        session()->put('import.fileHeadings', Excel::toCollection(new HeadingRowImport(), $data['file'])->first()->first()->toArray());
        session()->put('import.whatImporting', $data['what_importing']);
        session()->put('import.updateExistingRecords', $data['update_existing_records'] ?? 0);

        CacheImportCollection::dispatch(session('import.sessionId'), session('import.filePath'));

        return redirect()->route('import.map.index');
    }
}
