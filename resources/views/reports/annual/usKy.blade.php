@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 130px;">Date Received</th>
            <th style="width: 250px;">Species</th>
            <th style="width: auto;">Release Location</th>
            <th style="width: 130px;">Date of Release</th>
            <th style="width: 150px;">If Not Released - Outcome</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>@if($admission->patient->disposition === 'Released') {!! $admission->patient->disposition_locale !!} @endif</td>
                <td>@if($admission->patient->disposition === 'Released') {{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }} @endif</td>
                <td>
                    @if($admission->patient->disposition !== 'Released')
                        {{ format_disposition($admission->patient->disposition) }}<br>
                        @if($admission->patient->disposition === 'Transferred')
                            {!! $admission->patient->disposition_locale !!}
                        @endif
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
