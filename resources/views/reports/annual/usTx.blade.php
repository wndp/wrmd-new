@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width:250px">Species</th>
            <th style="width:150px">Arrival Date</th>
            <th style="width:auto;">Nature of Injury</th>
            <th style="width:155px">Final Disposition and Date</th>
            <th style="width:230px">Release Location OR Transferred to</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->admitted_at->format($account->settingsStore()->get('date_format')) }}</td>
                <td>{{ $admission->patient->disposition }}</td>
                <td>
                    {{ $admission->patient->disposition }} <br>
                    {{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}
                </td>
                <td>{{ $admission->patient->disposition_location }}</td>
            </tr>
        @endforeach
    <tbody>
</table>

<div class="row signature-margin">
    <div class="col-md-3"><div class="text-field">Date</div></div>
    <div class="col-md-9"><div class="text-field">Signature of Permittee(s)</div></div>
</div>

@stop
