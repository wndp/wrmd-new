@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 90px;">Case #</th>
            <th style="width: 130px;">Date Acquired</th>
            <th style="width: 130px;">Species</th>
            <th style="width: 130px;">Origination</th>
            <th style="width: auto;">Nature of Illness / Injury</th>
            <th style="width: 130px;">Disposition Date</th>
            <th style="width: 150px;">Disposition</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <?php
            $disposition = format_disposition($admission->patient->disposition);
            $dispositionLocation = in_array($disposition, ['Released', 'Transferred']) ? '<br>' . $admission->patient->disposition_locale : '';
            ?>
            <tr>
                <td>{{ $admission->caseNumber }}</td>
                <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->city_found }}</td>
                <td>{{ $admission->patient->diagnosis }}</td>
                <td>{{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
                <td>{!! $disposition . $dispositionLocation !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
