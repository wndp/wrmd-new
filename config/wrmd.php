<?php

return [

    'donateHeader' => env('DONATE_HEADER', false),

    'date_format' => 'M j, Y',

    'date_time_format' => 'M j, Y g:i a',

    /*
     * Fields to always select for a list view.
     */
    'alwaysListFields' => [
        'admissions.case_id',
        'admissions.case_year',
        'patients.common_name',
        'patients.admitted_at',
        'patients.rescuer_id',
    ],

    /*
     * Default fields to select for a list view.
     */
    // 'defaultListFields' => [
    //     'patient_locations.facility',
    //     'patients.band',
    //     'patients.disposition',
    //     'patients.dispositioned_at',
    // ],

    'reporting' => [
        'pdf_driver' => env('PDF_DRIVER', 'domPdf'),
    ]

];
