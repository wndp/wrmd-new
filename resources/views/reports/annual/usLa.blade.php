@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 130px;">Date Received</th>
            <th style="width: 130px;">Date Released</th>
            <th style="width: auto;">Species</th>
            <th style="width: 200px;">Reason For Intake</th>
            <th style="width: 130px;">Distemper Symptoms &#x2713;</th>
            <th style="width: 135px;">Outcome</th>
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
                <td>
                    @if($admission->patient->disposition === 'Released')
                        {{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}
                    @endif
                </td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->reasons_for_admission }}</td>
                <td></td>
                <td>{{ format_disposition($admission->patient->disposition) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
