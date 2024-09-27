<?php

namespace App\Http\Controllers\Importing;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MappingController extends Controller
{
    //use FacilitatesImporting;

    /**
     * Submitted values in the mappedHeadings array under validation.
     *
     * @var array
     */
    private $submittedValues = [];

    /**
     * Duplicate values in the mappedHeadings array under validation.
     *
     * @var array
     */
    private $duplicateValues = [];

    /**
     * Constructor.
     */
    public function __construct()
    {
        /*
         * Validate the submitted values contain all the required fields.
         */
        Validator::extend('has_required_fields', function ($attribute, $value, $parameters, $validator) {
            $this->submittedValues = $value;
            $requiredFields = $this->filteredRequiredFields();

            return empty(array_filter($requiredFields)) || (is_array($value) && in_array_multiple($requiredFields, $value));
        });

        Validator::replacer('has_required_fields', function ($message, $attribute, $rule, $parameters) {
            $diff = array_diff($this->requiredFields, $this->submittedValues);

            return str_replace(':attribute', implode(', ', $diff), 'The following required fields were not selected: :attribute.');
        });

        /*
         * Validate the submitted values contains an array of unique values.
         */
        Validator::extend('has_unique_values', function ($attribute, $value, $parameters, $validator) {
            if (! is_array($value)) {
                return false;
            }

            $this->duplicateValues = array_filter(array_unique(array_diff_assoc(
                $value,
                array_unique($value)
            )));

            return count($this->duplicateValues) === 0;
        });

        /*
         * Replace all place-holders for the uniqueValues rule.
         */
        Validator::replacer('has_unique_values', function ($message, $attribute, $rule, $parameters) {
            $intersect = array_intersect_key(fields()->getLabels()->all(), array_flip($this->duplicateValues));

            $last = array_pop($intersect);
            $string = count($intersect) ? implode(', ', $intersect).' & '.$last.' have' : $last.' has';

            return str_replace(':attribute', $string, 'The :attribute field been used more than once.');
        });
    }

    /**
     * Display the form for mapping file headings.
     *
     * @return Illuminate\Http\Response
     */
    public function index()
    {
        $extensionFields = event('import.mapFields.'.session()->get('import.whatImporting'));

        if ($extensionFields = array_shift($extensionFields)) {
            $wrmdFields = $extensionFields
                ->getLabels()
                ->groupBytable(true)
                ->toArray();
        } else {
            $wrmdFields = fields('us')
                ->byTable(['patients', 'people', 'exams'])
                ->filterOut('importable')
                ->getLabels()
                ->groupBytable(true)
                ->toArray();
        }

        $worksheet = (new PreviewImport())->toCollection(session('import.filePath'), 's3-accounts')->first();

        $sampleValues = collect(array_fill_keys(session('import.fileHeadings'), null))->map(function ($value, $heading) use ($worksheet) {
            return $worksheet->map(function ($row) use ($heading) {
                return $row->get($heading);
            })->filter();
        });

        $shouldSearchForExistingRecords = $this->shouldSearchForExistingRecords();
        $wrmdExistingColumns = $this->wrmdExistingColumns();

        return Inertia::render('Maintenance/Importing/Map', compact('shouldSearchForExistingRecords', 'wrmdExistingColumns', 'wrmdFields', 'sampleValues'));
    }

    /**
     * Store a lookup map of the file headings in memory.
     */
    public function store(): RedirectResponse
    {
        $shouldRequire = $this->shouldSearchForExistingRecords();

        request()->validate([
            'mapped_headings' => 'required|has_required_fields|has_unique_values',
            // validate that all rows have common_name
            // validate that all rows have admitted_at
            // validate that all rows have disposition
            'spreadsheet_existing_column' => Rule::requiredIf($shouldRequire),
            'wrmd_existing_column' => Rule::requiredIf($shouldRequire),
        ], [
            'spreadsheet_existing_column.required' => 'Choose a spreadsheet column to match to existing records.',
            'wrmd_existing_column.required' => 'Choose a WRMD field to search for your spreadsheet column\'s value.',
        ]);

        $mappedHeadings = [];
        foreach (array_filter(request('mapped_headings')) as $i => $field) {
            $mappedHeadings[$field] = session('import.fileHeadings')[$i];
        }

        session()->put('import.mappedHeadings', $mappedHeadings);
        session()->put('import.spreadsheetExistingColumn', request('spreadsheet_existing_column'));
        session()->put('import.wrmdExistingColumn', request('wrmd_existing_column'));

        return redirect()->route('import.translation.index');
    }
}
