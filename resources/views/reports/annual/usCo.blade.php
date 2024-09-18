@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<h2 style="margin-top: 100px">Admissions/Dispositions Year-End Report Form</h2>

<table class="print-table">
    <thead>
        <tr>
            <th colspan="2">Animal Info</th>
            <th colspan="3" style="border-left:1px solid #dbdbdb; padding-left: 20px">Acquisition Info</th>
            <th colspan="4" style="border-left:1px solid #dbdbdb; padding-left: 20px">Disposition Info</th>
            <th></th>
        </tr>
        <tr>
            <th style="width: 130px;">Species</th>
            <th style="width: auto;">Sex</th>
            <th style="width: 130px; border-left:1px solid #dbdbdb; padding-left: 20px">Acquisition Date</th>
            <th style="width: 150px;">Origin Location</th>
            <th style="width: 110px;">Cause of Admission and Treatment</th>
            <th style="width: 150px; border-left:1px solid #dbdbdb; padding-left: 20px">Final Disposition</th>
            <th style="width: 130px;">Disposition Date</th>
            <th>Disposition Location</th>
            <th>Comments</th>
            <th>Initials of Rehabilitator</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->intake_exam->sex }}</td>
                <td style="border-left:1px solid #dbdbdb; padding-left: 20px">{{ $admission->patient->admitted_at_formatted}}</td>
                <td>{!! $admission->patient->location_found !!}</td>
                <td>
                    {{ $admission->patient->reasons_for_admission }}
                    {{ $admission->patient->intake_exam->treatment }}
                </td>
                <td style="border-left:1px solid #dbdbdb; padding-left: 20px">{{ format_disposition($admission->patient->disposition) }}</td>
                <td>{{ $admission->patient->dispositioned_at_formatted }}</td>
                <td>{!! $admission->patient->disposition_locale !!}</td>
                <td>{{ $admission->patient->reason_for_disposition }}</td>
                <td>{{ str_initials($admission->patient->dispositioned_by) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="page-break-before: always">

<h2 style="margin-top: 100px">Prior Report Form</h2>
<p>PRIOR REPORT for animals aquired during the prior year and disposed of this year. All animals must trace back to last year's report.</p>

<table class="print-table">
    <thead>
        <tr>
            <th colspan="2">Animal Info</th>
            <th colspan="3" style="border-left:1px solid #dbdbdb; padding-left: 20px">Acquisition Info</th>
            <th colspan="4" style="border-left:1px solid #dbdbdb; padding-left: 20px">Disposition Info</th>
        </tr>
        <tr>
            <th style="width: 130px;">Species</th>
            <th style="width: auto;">Sex</th>
            <th style="width: 130px; border-left:1px solid #dbdbdb; padding-left: 20px">Acquisition Date</th>
            <th style="width: 150px;">Origin Location</th>
            <th style="width: 110px;">Cause of Admission and Treatment</th>
            <th style="width: 150px; border-left:1px solid #dbdbdb; padding-left: 20px">Final Disposition</th>
            <th style="width: 130px;">Disposition Date</th>
            <th>Disposition Location</th>
            <th>Comments</th>
        </tr>
    </thead>
    <tbody>
        @foreach($priorAdmissions as $admission)
            <tr>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->intake_exam->sex }}</td>
                <td style="border-left:1px solid #dbdbdb; padding-left: 20px">{{ $admission->patient->admitted_at_formatted}}</td>
                <td>{!! $admission->patient->location_found !!}</td>
                <td>
                    {{ $admission->patient->reason_for_admission }}
                    {{ $admission->patient->intake_exam->treatment }}
                </td>
                <td style="border-left:1px solid #dbdbdb; padding-left: 20px">{{ format_disposition($admission->patient->disposition) }}</td>
                <td>{{ $admission->patient->dispositioned_at_formatted }}</td>
                <td>{!! $admission->patient->disposition_locale !!}</td>
                <td>{{ $admission->patient->reason_for_disposition }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<p style="page-break-before: always">

<h2 style="margin-top: 100px">Annual Summary Report Form</h2>
<p>ANNUAL DISPOSITION SUMMARY of all animals acquired this year (INCLUDE the "priors" disposed of this year).</p>

<table class="print-table">
    <thead>
        <tr>
            <th style="width:250px">Common Name</th>
            <th style="width:75px">DOA</th>
            <th style="width:75px">EOA</th>
            <th style="width:75px">TOA</th>
            <th style="width:75px">E</th>
            <th style="width:75px">D</th>
            <th style="width:75px">R</th>
            <th style="width:75px">T</th>
            <th style="width:75px">P</th>
            <th style="width:auto">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($summary as $dispositions)
            <tr>
                <td>{{ $dispositions->common_name }}</td>
                <td>{{ $dispositions->doa }}</td>
                <td>{{ $dispositions->euthanized_in_24 }}</td>
                <td>{{ $dispositions->transferred_in_24 }}</td>
                <td>{{ $dispositions->euthanized_after_24 }}</td>
                <td>{{ $dispositions->died }}</td>
                <td>{{ $dispositions->released }}</td>
                <td>{{ $dispositions->transferred }}</td>
                <td>{{ $dispositions->pending }}</td>
                <td>{{ $dispositions->total }}</td>
            </tr>
        @endforeach
        <tr>
            <th>Totals</th>
            <td>{{ $summary->sum('doa') }}</td>
            <td>{{ $summary->sum('euthanized_in_24') }}</td>
            <td>{{ $summary->sum('transferred_in_24') }}</td>
            <td>{{ $summary->sum('euthanized_after_24') }}</td>
            <td>{{ $summary->sum('died') }}</td>
            <td>{{ $summary->sum('released') }}</td>
            <td>{{ $summary->sum('transferred') }}</td>
            <td>{{ $summary->sum('pending') }}</td>
            <td>{{ $summary->sum('total') }}</td>
        </tr>
    </tbody>
</table>

@stop
