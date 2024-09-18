@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>
@if(isset($dateFrom))
    <h2 class="print-sub-title">Between: {{ $dateFrom }} to {{ $dateTo }}</h2>
@endif

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 130px;">Date Admitted</th>
            <th style="width: 130px;">Species</th>
            <th style="width: 110px;">Sex / Age</th>
            <th style="width: 150px;">Location Obtained</th>
            <th style="width: auto;">Diagnosis / Cause</th>
            <th style="width: 130px;">Disposition / Date</th>
            <th style="width: 150px;">Location Released / Transfered</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ $admission[0] }}</td>
                <td>{{ $admission[1] }}</td>
                <td>{!! $admission[2] !!}</td>
                <td>{!! $admission[3] !!}</td>
                <td>{{ $admission[4] }}</td>
                <td>{!! $admission[5] !!}</td>
                <td>{{ $admission[6] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
