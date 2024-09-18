@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 130px;">Date Admitted</th>
            <th style="width: 130px;">Species Name</th>
            <th style="width: 130px;">Sex / Age</th>
            <th style="width: 200px;">Location Obtained</th>
            <th style="width: auto;">Diagnosis</th>
            <th style="width: 110px;">Disposition / Date</th>
            <th style="width: 200px;">Location Released</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ $admission->patient->admitted_at->format($account->settingsStore()->get('date_format')) }}</td>
                <td>
                    {{ $admission->patient->common_name }}
                    <i>{{ $admission->patient->species->scientific_name }}</i>
                </td>
                <td>{{ implode(' / ', array_filter([$admission->patient->intakeExam?->sex, $admission->patient->intakeExam?->age_unit])) }}</td>
                <td>{!! $admission->patient->location_found !!}</td>
                <td>{{ implode('. ', [$admission->patient->reasons_for_admission, $admission->patient->diagnosis]) }}</td>
                <td>
                    {{ format_disposition($admission->patient->disposition) }}
                    {{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}
                </td>
                <td>{{ $admission->patient->disposition_location.' '.$admission->patient->disposition_subdivision }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
