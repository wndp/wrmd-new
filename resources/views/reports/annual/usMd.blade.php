@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 120px;">Date</th>
            <th style="width: auto;">Animal / Problem</th>
            <th style="width: 300px;">Location Found</th>
            <th style="width: 140px;">Disposition</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
                <td>
                    {{ $admission->patient->common_name }} /
                    {{ $admission->patient->diagnosis }}
                </td>
                <td>{!! $admission->patient->location_found !!}</td>
                <td>{{ format_disposition($admission->patient->disposition) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
