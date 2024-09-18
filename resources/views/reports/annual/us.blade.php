<?php
$transferTypes = \App\Domain\Patients\PatientOptions::$transferTypes;
$transferTypes['Released'] = 'R';
$transferTypes['Continued care'] = 'C';
$transferTypes['Education or scientific research (individual)'] = 'E/S';
$transferTypes['Education or scientific research (institute)'] = 'E/S';
$transferTypes['Falconry or raptor propagation'] = 'F/P';
$transferTypes['Other'] = 'O';
$transferTypes[''] = '';
?>

@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>
<h2 class="print-sub-title">USFWS Form 3-202-4</h2>

@include('reports.annual.partials.header')

<h3 class="print-caption mt-10">Disposition Codes</h3>
<dl class="flex text-black text-base mt-1">
    <dt class="font-bold mr-2">R</dt> <dd class="mr-4">Released</dd>
    <dt class="font-bold mr-2">T</dt> <dd class="mr-4">Transferred</dd>
    <dt class="font-bold mr-2">P</dt> <dd class="mr-4">Pending</dd>
    <dt class="font-bold mr-2">E</dt> <dd class="mr-4">Euthanized</dd>
    <dt class="font-bold mr-2">D</dt> <dd class="mr-4">Died</dd>
    <dt class="font-bold mr-2">DOA</dt> <dd>Dead on Arrival</dd>
</dl>

<h3 class="print-caption mt-10">Transfer Codes</h3>
<dl class="flex text-black text-base mt-1">
    <dt class="font-bold mr-2">R</dt> <dd class="mr-4">Released</dd>
    <dt class="font-bold mr-2">C</dt> <dd class="mr-4">Continued Care</dd>
    <dt class="font-bold mr-2">E/S</dt> <dd class="mr-4">Education or Scientific Research</dd>
    <dt class="font-bold mr-2">F/P</dt> <dd class="mr-4">Falconry or Raptor Propagation</dd>
    <dt class="font-bold mr-2">O</dt> <dd>Other</dd>
</dl>

<table class="print-table mt-10">
    <caption>A. Birds Held Over</caption>
    <thead>
        <tr>
            <th style="width:250px">Common Name</th>
            <th style="width:130px">Date Acquired</th>
            <th style="width:auto">Nature of Injury</th>
            <th style="width:120px">Disposition</th>
            <th style="width:175px">Date of Disposition</th>
        </tr>
    </thead>
    <tbody>
        @foreach($heldOverEagles as $heldOverEagle)
        <tr>
            <td>{{ $heldOverEagle->common_name }}</td>
            <td>{{ format_date($heldOverEagle->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>{{ $heldOverEagle->diagnosis }}</td>
            <td>{{ format_disposition($heldOverEagle->disposition) }}</td>
            <td>{{ format_date($heldOverEagle->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
        </tr>
        @endforeach
        @foreach($heldOverBirds as $heldOverBird)
        <tr>
            <td>{{ $heldOverBird->common_name }}</td>
            <td>{{ format_date($heldOverBird->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>{{ $heldOverBird->diagnosis }}</td>
            <td>{{ format_disposition($heldOverBird->disposition) }}</td>
            <td>{{ format_date($heldOverBird->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="print-table mt-10">
    <caption>B. New Acquisitions</caption>
    <thead>
        <tr>
            <th style="width:250px">Common Name</th>
            <th style="width:75px;">Total</th>
            <th style="width:75px;">R</th>
            <th style="width:75px;">T</th>
            <th style="width:75px;">P</th>
            <th style="width:75px;">E</th>
            <th style="width:75px;">D</th>
            <th style="width:auto;">DOA</th>
        </tr>
    </thead>
    <tbody>
        @foreach($eagleAcquisitions as $dispositions)
        <tr>
            <td>{{ $dispositions->common_name }}</td>
            <td>{{ $dispositions->total }}</td>
            <td>{{ $dispositions->released }}</td>
            <td>{{ $dispositions->transferred }}</td>
            <td>{{ $dispositions->pending }}</td>
            <td>{{ $dispositions->euthanized }}</td>
            <td>{{ $dispositions->died }}</td>
            <td>{{ $dispositions->doa }}</td>
        </tr>
        @endforeach

        @foreach($birdAcquisitions as $dispositions)
        <tr>
            <td>{{ $dispositions->common_name }}</td>
            <td>{{ $dispositions->total }}</td>
            <td>{{ $dispositions->released }}</td>
            <td>{{ $dispositions->transferred }}</td>
            <td>{{ $dispositions->pending }}</td>
            <td>{{ $dispositions->euthanized }}</td>
            <td>{{ $dispositions->died }}</td>
            <td>{{ $dispositions->doa }}</td>
        </tr>
        @endforeach

        <tr>
            <th>Grand Totals</th>
            <td>{{ $eagleAcquisitions->sum('total') + $birdAcquisitions->sum('total') }}</td>
            <td>{{ $eagleAcquisitions->sum('released') + $birdAcquisitions->sum('released') }}</td>
            <td>{{ $eagleAcquisitions->sum('transferred') + $birdAcquisitions->sum('transferred') }}</td>
            <td>{{ $eagleAcquisitions->sum('pending') + $birdAcquisitions->sum('pending') }}</td>
            <td>{{ $eagleAcquisitions->sum('euthanized') + $birdAcquisitions->sum('euthanized') }}</td>
            <td>{{ $eagleAcquisitions->sum('died') + $birdAcquisitions->sum('died') }}</td>
            <td>{{ $eagleAcquisitions->sum('doa') + $birdAcquisitions->sum('doa') }}</td>
        </tr>
    </tbody>
</table>

<table class="print-table mt-10">
    <caption>C. Reported Injuries / Mortalities</caption>
    <thead>
        <tr>
            <th style="width:250px">Common Name</th>
            <th style="width:130px">Date Acquired</th>
            <th style="width:auto">Cause / Nature of Injury</th>
            <th style="width:120px">Disposition</th>
            <th style="width:215px">Source (County &amp; State)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($reportedInjuries as $reportedInjury)
        <tr>
            <td>{{ $reportedInjury->common_name }}</td>
            <td>{{ format_date($reportedInjury->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>{{ $reportedInjury->reasons_for_admission }} / {{ $reportedInjury->diagnosis }}</td>
            <td>{{ format_disposition($reportedInjury->disposition) }}</td>
            <td>{{ $reportedInjury->county_found .', '.$reportedInjury->subdivision_found }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="print-table mt-10">
    <caption>D. Still Pending</caption>
    <thead>
        <tr>
            <th style="width:250px">Common Name</th>
            <th style="width:130px">Date Acquired</th>
            <th style="width:auto">Nature of Injury</th>
            <th style="width:240px">Proposed Disposition (R, T)</th>
        </tr>
    </thead>
    <tbody>
        @foreach($stillPendings as $stillPending)
        <tr>
            <td>{{ $stillPending->common_name }}</td>
            <td>{{ format_date($stillPending->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>{{ $stillPending->diagnosis }}</td>
            <td></td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="print-table mt-10">
    <caption>E. Transfers</caption>
    <thead>
        <tr>
            <th style="width:250px">Common Name</th>
            <th style="width:auto">Name and Permit Number or Address</th>
            <th style="width:130px">Date</th>
            <th style="width:120px">Purpose</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transfers as $transfer)
        <tr>
            <td>{{ $transfer->common_name }}</td>
            <td>{{ $transfer->disposition_location .' '. $transfer->disposition_subdivision }}</td>
            <td>{{ format_date($transfer->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>@if(array_key_exists($transfer->transfer_type, $transferTypes)) {{ $transferTypes[$transfer->transfer_type] }} @endif</td>
        </tr>
        @endforeach
    </tbody>
</table>

<h3 class="print-caption mt-10">F. Optional - Disease &amp; Contaminants</h3>
<p class="print-text"><em class="italic">Not included on this document.</em></p>

<p class="print-text mt-10"><strong class="font-bold">CERTIFICATION:</strong> I Certify that the above information is true and correct to the best of my knowledge. I understand that any false statement herein may subject me to the criminal penalties of 18 U.S.C. 1001.</p>

<table class="print-table mt-10">
    <tbody>
        <tr class="border-b border-black">
            <td style="width:400px;">Signature</td>
            <td style="width:200px;"></td>
            <td style="width:400px;">Date</td>
        </tr>
    </tbody>
</table>

@stop
