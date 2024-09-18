@extends('layouts.report')

@section('body')

<h1>{{ $year }} {{ $title }}</h1>

@include('reporting.reports.annual.partials.header')

<table class="table">
    <thead>
        <tr>
            <th>Species</th>
            <th>Date Received</th>
            <th>Age / Weight</th>
            <th>Obtained From</th>
            <th>Site of Origin</th>
            <th>Rabies Advisory Notice?</th>
            <th>Cause of Distress</th>
            <th>Disposition</th>
            <th>Date</th>
        </tr>
    </thead>
    @foreach($admissions as $admission)
        <tr>
            <td>{{ $admission->patient->common_name }}</td>
            <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>{!! collect([$admission->patient->intake_exam->full_age, $admission->patient->intake_exam->full_weight])->filter()->implode(' / ') !!}</td>
            <td>
                {!! $admission->patient->rescuer->full_name !!}<br>
                {!! collect([$admission->patient->rescuer->address, $admission->patient->rescuer->city])->filter()->implode(', ') !!}<br>
                {!! $admission->patient->rescuer->phone !!}
            </td>
            <td>{!! collect([$admission->patient->address_found, $admission->patient->city_found])->filter()->implode(', ') !!}</td>
            <td></td>
            <td>{!! $admission->patient->reasons_for_admission !!}</td>
            <td>{{ format_disposition($admission->patient->disposition) }}</td>
            <td>{{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
        </tr>
    @endforeach
</table>

@stop
