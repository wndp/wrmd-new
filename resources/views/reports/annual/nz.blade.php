@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>
<h2 class="print-sub-title">Between: {{ $dateFrom }} to {{ $dateTo }}</h2>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width:150px">Date Received</th>
            <th style="width:130px">Species</th>
            <th style="width:200px">Location Found</th>
            <th style="width:200px">Assessment</th>
            <th style="width:110px">Vet referral (y/n)</th>
            <th style="width:auto">Treatment</th>
            <th style="width:110px">Outcome</th>
            <th style="width:150px">Outcome Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ $admission->patient->admitted_at->format('m/d/y') }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{!! $admission->patient->getLocationFoundAttribute(null, false) !!}</td>
                <td>{{ "{$admission->patient->reasons_for_admission} {$admission->patient->diagnosis}" }}</td>
                <td>{{ $admission->patient->veterinary_referral }}</td>
                <td>{{ $admission->patient->intake_exam->treatment }}</td>
                <td>{{ format_disposition($admission->patient->disposition) }}</td>
                <td>@if($admission->patient->dispositioned_at) {{ $admission->patient->dispositioned_at->format('m/d/y') }} @endif</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
