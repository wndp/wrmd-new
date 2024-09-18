@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 180px;">Species</th>
            <th style="width: 130px;">Age</th>
            <th style="width: 150px;">Location Found</th>
            <th style="width: auto;">Cause For Admission</th>
            <th style="width: 150px;">Date Admitted</th>
            <th style="width: 150px;">Disposition</th>
            <th style="width: 150px;">Resolution Date</th>
            <th style="width: 130px;">Disposition Location</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->intake_exam->full_age }}</td>
                <td>{!! $admission->patient->location_found !!}</td>
                <td>{{ $admission->patient->reasons_for_admission }}</td>
                <td>{{ $admission->patient->admitted_at_formatted }}</td>
                <td>{{ format_disposition($admission->patient->disposition) }}</td>
                <td>{{ $admission->patient->dispositioned_at_formatted }}</td>
                <td>{!! $admission->patient->disposition_locale !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
