@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>
<h2 class="print-sub-title">Between: {{ $dateFrom }} to {{ $dateTo }}</h2>

<table class="print-table mt-20">
    <thead>
        <tr>
            <th style="width: 130px">Case #</th>
            <th style="width: 200px">Common Name</th>
            <th style="width: 180px">Date Admitted</th>
            <th style="width: 150px">Disposition</th>
            <th style="width: 150px">Disposition Date</th>
            <th style="width: auto">Caregiver(s)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ $admission->case_number }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->admitted_at_formatted }}</td>
                <td>{{ $admission->patient->disposition }}</td>
                <td>{{ $admission->patient->dispositioned_at_formatted }}</td>
                <td>{{ $admission->patient->locations->map(function ($location) { return $location->area; })->implode(', ') }}</td>
            </tr>
        @endforeach
        <tr>
            <th>Total Patients</th>
            <td colspan="5">{{ $admissions->count() }}</td>
        </tr>
    </tbody>
</table>

@stop
