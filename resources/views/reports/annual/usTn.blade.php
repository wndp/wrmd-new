@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 100px">Species</th>
            <th style="width: 130px">Date Received</th>
            <th style="width: 200px">Source</th>
            <th style="width: auto">Problem</th>
            <th style="width: 110px">Disposition</th>
            <th style="width: 130px">Date Disposed</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->admitted_at->format($account->settingsStore()->get('date_format')) }}</td>
                <td>{{ $admission->patient->rescuer->identifier }}<br>{!! $admission->patient->rescuer->full_address !!}</td>
                <td>{{ $admission->patient->reasons_for_admission }}</td>
                <td>{{ format_disposition($admission->patient->disposition) }}</td>
                <td>{{ $admission->patient->dispositioned_at_formatted }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
