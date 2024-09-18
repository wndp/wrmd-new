@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th>From WRARI Clinic (Y/N)</th>
            <th>Intake Date</th>
            <th>Species</th>
            <th>Sex</th>
            <th>Age (J/A)</th>
            <th>Town of Origin</th>
            <th>Specific Location of Origin</th>
            <th>Reason for Intake</th>
            <th>Treatment</th>
            <th>Name of Transporter</th>
            <th>Address of Transporter</th>
            <th>Telephone No. of Transporter</th>
            <th>Disposition</th>
            <th>Date and Location of Disposition</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td></td>
                <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->intake_exam->sex }}</td>
                <td>{{ $admission->patient->intake_exam->age_unit === 'Adult' ? 'A' : 'J' }}</td>
                <td>{{ $admission->patient->city_found }}</td>
                <td>{{ $admission->patient->address_found }}</td>
                <td>{{ $admission->patient->reasons_for_admission }}</td>
                <td>{{ $admission->patient->intake_exam->treatment }}</td>
                <td>{{ $admission->patient->rescuer->identifier }}</td>
                <td>{{ $admission->patient->rescuer->fullAddress(false) }}</td>
                <td>{{ $admission->patient->rescuer->phone }}</td>
                <td>{{ format_disposition($admission->patient->disposition) }}</td>
                <td>
                    {{ format_date($admission->patient->dispositioned_at) }}<br>
                    {{ $admission->patient->disposition_location }}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="page-break-before: always">

<p>
    Department of Environmental Management Division of Fish & Wildlife<br>
    277 Great Neck Road<br>
    West Kingston RI 02892<br>
    (401) 789-0281x35<br>
    Sarah.Riley@dem.ri.gov<br>
</p>

<div class="row margin-bottom signature-margin">
    <div class="col-md-6"><div class="text-field">Signature</div></div>
    <div class="col-md-6"><div class="text-field">Date</div></div>
</div>

@stop
