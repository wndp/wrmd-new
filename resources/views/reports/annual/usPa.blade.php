@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>
<h2 class="print-sub-title">Between: {{ $dateFrom }} to {{ $dateTo }}</h2>

@include('reports.annual.partials.header')

<table class="print-table">
    @foreach($admissions as $admission)
        <?php
        $rescuer = $admission->patient->rescuer;
        $originSource  = empty($rescuer->organization) ? $rescuer->first_name.' '.$rescuer->last_name : $rescuer->organization;
        $originSource .= '<br>' . $rescuer->county;
        ?>
        <tr>
            <td style="width:150px;">
                <strong style="font-size: 15px;">Date Received</strong><br>
                {{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}
            </td>
            <td style="width:100px;">
                <strong style="font-size: 15px;">Number</strong><br>
                {{ $admission->caseNumber }}
            </td>
            <td style="width:175px;">
                <strong style="font-size: 15px;">Species (common name)</strong><br>
                {{ $admission->patient->common_name }}
            </td>
            <td style="width:100px;">
                <strong style="font-size: 15px;">Age</strong><br>
                {{ $admission->patient->intake_exam->age . ' ' . $admission->patient->intake_exam->age_unit }}
            </td>
            <td style="width:100px;">
                <strong style="font-size: 15px;">Sex</strong><br>
                {{ $admission->patient->intake_exam->sex }}
            </td>
            <td style="width:150px;">
                <strong style="font-size: 15px;">Location Where Found</strong><br>
                {{ $admission->patient->city_found . ' ' . $admission->patient->subdivision_found }}
            </td>
            <td style="width:auto;">
                <strong style="font-size: 15px;">Received From</strong><br>
                {!! $originSource !!}
            </td>
        </tr>
        <tr>
            <td colspan="2" style="border-top: none">
                <strong style="font-size: 15px;">Injury</strong><br>
                {{ $admission->patient->diagnosis }}
            </td>
            <td style="border-top: none">
                <strong style="font-size: 15px;">Cause of Injury</strong><br>
                {{ $admission->patient->reasons_for_admission }}
            </td>
            <td colspan="3" style="border-top: none">
                <strong style="font-size: 15px;">Disposition</strong><br>
                {{ format_disposition($admission->patient->disposition) }}
            </td>
            <td style="border-top: none">
                <strong style="font-size: 15px;">Date</strong><br>
                {{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}
            </td>
        </tr>
    @endforeach
</table>

<h2>Total Mammals and Birds</h2>
<div class="row">
    <div class="col-md-4">
        <strong>Mammals</strong><br>
        <p>{{ $mammals }}</p>
    </div>
    <div class="col-md-4">
        <strong>Passerines</strong><br>
        <p>{{ $passerines }}</p>
    </div>
    <div class="col-md-4">
        <strong>Raptors</strong><br>
        <p>{{ $raptors }}</p>
    </div>
</div>

@stop
