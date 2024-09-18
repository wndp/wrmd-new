@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 130px">Intake Date</th>
            <th style="width: 100px">Common Name</th>
            <th style="width: 145px">Presenting Issue</th>
            <th style="width: 100px">County</th>
            <th style="width: 110px">Disposition</th>
            <th style="width: 100px">County Released</th>
            <th style="width: 130px">Date of Release</th>
            <th style="width: auto">GPS Coordinators or Address of Release Site</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ $admission->patient->admitted_at->format($account->settingsStore()->get('date_format')) }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->reasons_for_admission }}</td>
                <td>{{ $admission->patient->county_found }}</td>
                <td>{{ format_disposition($admission->patient->disposition) }}</td>
                <td>@if($admission->patient->disposition === 'Released') {{ $admission->patient->disposition_county }} @endif</td>
                <td>@if($admission->patient->disposition === 'Released') {{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }} @endif</td>
                <td>@if($admission->patient->disposition === 'Released') {!! $admission->patient->disposition_locale !!} @endif</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
