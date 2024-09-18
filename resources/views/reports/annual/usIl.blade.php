@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 130px;">Date Received</th>
            <th style="width: 130px;">Species Handled</th>
            <th style="width: 150px;">Origin of Specimen</th>
            <th style="width: auto;">Diagnosis</th>
            <th style="width: 130px;">Date of Disposition</th>
            <th style="width: 110px;">Disposition</th>
            <th style="width: 150px;">Release Location County/Property Owner Name</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <?php
            $rescuer = $admission->patient->rescuer;
            $originSource  = empty($rescuer->organization) ? $rescuer->first_name.' '.$rescuer->last_name : $rescuer->organization;
            $originSource .= '<br>' . $rescuer->county;
            ?>
            <tr>
                <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{!! $admission->patient->location_found !!}</td>
                <td>{{ $admission->patient->diagnosis }}</td>
                <td>{{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
                <td>{{ str_initials(format_disposition($admission->patient->disposition)) }}</td>
                <td>{{ $admission->patient->disposition_location.' '.$admission->patient->disposition_subdivision }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
