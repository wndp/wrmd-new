@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 130px;">Species</th>
            <th style="width: auto;">Condition of Animal</th>
            <th style="width: 130px;">Date Animal Received</th>
            <th style="width: 150px;">Name/Address of Donor or other source</th>
            <th style="width: 110px;">Method of Disposition</th>
            <th style="width: 130px;">Date of Disposition</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <?php
            $rescuer = $admission->patient->rescuer;
            $originSource  = empty($rescuer->organization) ? $rescuer->first_name.' '.$rescuer->last_name : $rescuer->organization;
            $originSource .= '<br>' . $rescuer->fullAddress();
            ?>
            <tr>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->diagnosis }}</td>
                <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
                <td>{!! $originSource !!}</td>
                <td>{{ format_disposition($admission->patient->disposition) }}</td>
                <td>{{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
