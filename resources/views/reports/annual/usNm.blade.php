@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 100px;">Case #</th>
            <th style="width: 180px;">Species</th>
            <th style="width: 130px;">Sex</th>
            <th style="width: 150px;">Origin Date / City / Source</th>
            <th style="width: auto;">Nature of Injury</th>
            <th style="width: 150px;">Condition Upon Receipt</th>
            <th style="width: 150px;">Treatment</th>
            <th style="width: 130px;">Disposition / Date / Location</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <?php
            $rescuer = $admission->patient->rescuer;
            $originSource  = empty($rescuer->organization) ? $rescuer->first_name.' '.$rescuer->last_name : $rescuer->organization;
            $originSource .= '<br>' . $admission->patient->county_found;
            ?>
            <tr>
                <td>{{ $admission->caseNumber }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>
                    {{ $admission->patient->intake_exam->sex }}
                </td>
                <td>
                    {{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}<br>
                    {{ $admission->patient->city_found }}<br>
                    {!! $originSource !!}
                </td>
                <td>{{ $admission->patient->reasons_for_admission }}</td>
                <td>{{ $admission->patient->diagnosis }}</td>
                <td>
                    {{ $admission->patient->intake_exam->treatment }}
                </td>
                <td>
                    {{ format_disposition($admission->patient->disposition) }}<br>
                    {{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}<br>
                    {!! $admission->patient->disposition_locale !!}
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
