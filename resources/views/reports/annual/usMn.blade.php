@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="table">
    <thead>
        <tr>
            <th colspan="4" style="text-align: center; border-right: 1px solid #777777">Received From</th>
            <th colspan="4" style="text-align: center; border-right: 1px solid #777777">Status when received</th>
            <th colspan="4" style="text-align: center;">Disposition</th>
        </tr>
        <tr>
            <th>Date</th>
            <th>Name</th>
            <th>Location/area Found</th>
            <th style="border-right: 1px solid #777777">County</th>
            <th>Species</th>
            <th>State</th>
            <th>Status</th>
            <th style="border-right: 1px solid #777777">If Sick/injured-brief description</th>
            <th>Date</th>
            <th>Code</th>
            <th>Release Location or Transfer Name/Address</th>
            <th>County</th>
            <th>Comments</th>
        </tr>
    </thead>
    @foreach($admissions as $admission)
        <tr>
            <td>{{ $admission->patient->admitted_at->format($account->settingsStore()->get('date_format')) }}</td>
            <td>{{ $admission->patient->rescuer->identifier }}</td>
            <td>{{ $admission->patient->location_found }}</td>
            <td style="border-right: 1px solid #777777">{{ $admission->patient->county_found }}</td>
            <td>{{ $admission->patient->common_name }}</td>
            <td>{{ $admission->patient->state }}</td>
            <td></td>
            <td style="border-right: 1px solid #777777">
                @if(in_array($admission->patient->state, ['I', 'S']))
                    {{ $admission->patient->reasons_for_admission }}
                @endif
            </td>
            <td>{{ $admission->patient->dispositioned_at ? $admission->patient->dispositioned_at->format($account->settingsStore()->get('date_format')) : ''}}</td>
            <td>{{ $admission->patient->disposition_for_minnesota }}</td>
            <td>@if(in_array($admission->patient->disposition, ['Released', 'Transferred'])) {{ $admission->patient->disposition_locale }} @endif</td>
            <td>{{ $admission->patient->disposition_county }}</td>
            <td>{{ $admission->patient->reason_for_disposition }}</td>
        </tr>
    @endforeach
</table>

@stop
