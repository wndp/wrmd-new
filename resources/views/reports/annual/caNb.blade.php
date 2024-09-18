@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 125px;">Date Received</th>
            <th style="width: 200px;">Species</th>
            <th style="width: 200px;">Found By</th>
            <th style="width: 200px;">Location Found</th>
            <th style="width: auto;">Reason for Admission</th>
            <th style="width: 200px;">Diagnosis</th>
            <th style="width: 150px;">Final Disposition</th>
            <th style="width: 125px;">Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ format_date($admission->patient->admitted_at, 'Y-m-d') }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->rescuer?->identifier }}</td>
                <td>{!! $admission->patient->getLocationFoundAttribute('', false) !!}</td>
                <td>{{ $admission->patient->reasons_for_admission }}</td>
                <td>{{ $admission->patient->diagnosis }}</td>
                <td>@if($admission->patient->disposition !== 'Pending') {{ $admission->patient->disposition }} @endif</td>
                <td>{{ format_date($admission->patient->dispositioned_at, 'Y-m-d') }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
