<?php

return [

    'show_donate_header' => env('DONATE_HEADER', false),

    // Date formats
    'date_format' => 'D, M j, Y',
    'date_time_format' => 'M j, Y g:i a',

    /*
     * Fields to always select for a list view.
     */
    // 'always_list_fields' => [
    //     'admissions.case_id',
    //     'admissions.case_year',
    //     'patients.common_name',
    //     'patients.admitted_at',
    //     'patients.rescuer_id',
    // ],

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
    ],

    'geocoders' => [
        //\App\Domain\Geocoding\Geocoders\WrmdGeocoder::class,
        \App\Actions\GeocodioGeocoder::class,
        \App\Actions\NullGeocoder::class,
    ],

];
