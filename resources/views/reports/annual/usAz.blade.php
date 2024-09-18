@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="table">
    <thead>
        <tr>
            <th style="width: 130px">Date Received</th>
            <th style="width: 100px">Species</th>
            <th style="width: 145px">Animal Received From</th>
            <th style="width: 100px">Location Animal Found</th>
            <th style="width: 110px">Age Class</th>
            <th style="width: 100px">Animals Condition</th>
            <th style="width: 130px">Date Disposed/Released</th>
            <th style="width: auto">Final Disposition</th>
            <th>Final Location of Animal</th>
            <th>Disposition of Remains</th>
        </tr>
    </thead>
    @foreach($admissions as $admission)
        <tr>
            <td>{{ $admission->patient->admitted_at->format($account->settingsStore()->get('date_format')) }}</td>
            <td>{{ $admission->patient->common_name }}</td>
            <td>{{ $admission->patient->rescuer->identifier }}</td>
            <td>{{ $admission->patient->location_found }}</td>
            <td>{{ $admission->patient->intakeExam->age_unit }}</td>
            <td>{{ $admission->patient->reasons_for_admission }}</td>
            <td>{{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>{{ format_disposition($admission->patient->disposition) }}</td>
            <td>{!! $admission->patient->disposition_locale !!}</td>
            <td></td>
        </tr>
    @endforeach
</table>

@stop
