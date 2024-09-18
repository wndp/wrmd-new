@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 200px;">Species<br>(Common Name)</th>
            <th style="width: 130px;">Date Acquired</th>
            <th style="width: 130px;">County of Origin</th>
            <th style="width: auto;">Condition When Acquired</th>
            <th style="width: 130px;">Name and City of Rescuer</th>
            <th style="width: 130px;">Name and City of Rehabber if Transfered</th>
            <th style="width: 130px;">Final Disposition and Date</th>
            <th style="width: 130px;">County of Release</th>
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
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
                <td>{{ $admission->patient->county_found }}</td>
                <td>{{ $admission->patient->diagnosis }}</td>
                <td>{!! $originSource !!}</td>
                <td>@if($admission->patient->disposition === 'Transferred') {{ $admission->patient->disposition_location }} @endif</td>
                <td>
                    {{ format_disposition($admission->patient->disposition) }}<br>
                    {{ format_date($admission->patient->dispositioned_at) }}
                </td>
                <td>@if($admission->patient->disposition === 'Released') {{ $admission->patient->disposition_county }} @endif</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="page-break-before: always">

<p>Mail, FAX or Email this Report and a Copy of the US Fish & Wildlife Service Form, if Used, for Each Calendar Year Must be Submitted by January 31 to:</p>

<p>
    Wildlife Division - Permit Specialist<br>
    Michigan Department of Natural Resources<br>
    PO Box 30444<br>
    Lansing MI  48909-7944<br>
    FAX: 517-335-6604<br>
    Email: reitzc@michigan.gov<br>
</p>

<div class="row margin-bottom signature-margin">
    <div class="col-md-6"><div class="text-field">Signature</div></div>
    <div class="col-md-6"><div class="text-field">Date</div></div>
</div>

@stop
