@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>
<h2 class="print-sub-title">CDFW Form FG540</h2>

@include('reports.annual.partials.header')

<h3 class="print-caption mt-12">Dispositions</h3>
<dl class="flex text-black text-base mt-4">
    <dt class="font-bold mr-2">R</dt> <dd class="mr-4">Released</dd>
    <dt class="font-bold mr-2">T</dt> <dd class="mr-4">Transferred</dd>
    <dt class="font-bold mr-2">P</dt> <dd class="mr-4">Pending</dd>
    <dt class="font-bold mr-2">E</dt> <dd class="mr-4">Euthanized</dd>
    <dt class="font-bold mr-2">D</dt> <dd class="mr-4">Died</dd>
    <dt class="font-bold mr-2">DOA</dt> <dd>Dead on Arrival</dd>
</dl>

<?php
$map = [
    'Amphibians' => $amphibianAcquisitions,
    'Birds' => $birdAcquisitions,
    'Mammals' => $mammalAcquisitions,
    'Reptiles' => $reptileAcquisitions
];
?>

@foreach($map as $caption => $acquisitions)
    <table class="print-table mt-8">
        <caption>{{ $caption }}</caption>
        <thead>
            <tr>
                <th style="width:250px">Common Name</th>
                <th style="width:75px">Total</th>
                <th style="width:75px">R</th>
                <th style="width:75px">T</th>
                <th style="width:75px">P</th>
                <th style="width:75px">E</th>
                <th style="width:75px">D</th>
                <th style="width:75px">DOA</th>
                <th class="border-l border-black" style="width:auto;">Re-united</th>
            </tr>
        </thead>
        <tbody>
            @foreach($acquisitions as $dispositions)
            <tr>
                <td>{{ $dispositions->common_name }}</td>
                <td>{{ $dispositions->total }}</td>
                <td>{{ $dispositions->released }}</td>
                <td>{{ $dispositions->transferred }}</td>
                <td>{{ $dispositions->pending }}</td>
                <td>{{ $dispositions->euthanized }}</td>
                <td>{{ $dispositions->died }}</td>
                <td>{{ $dispositions->doa }}</td>
                <td class="border-l border-black">{{ $dispositions->reunited }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endforeach

<p><strong>Total number of Mammals Received:</strong> {{ $mammalAcquisitions->sum('total') }}, <strong>Total Reptiles:</strong> {{ $reptileAcquisitions->sum('total') }}</p>

<p style="page-break-before: always">

<p>Please denote any of the following your facility encountered: die-off's or mortality events, significant disease events, unusual injuries, unusual occurrences involving sick or injured wildlife.</p>

@foreach(range(0, 8) as $i)
    <hr class="margin-top">
@endforeach

<div class="row signature-margin">
    <div class="col-md-2 label">Reported By:</div>
    <div class="col-md-6"><div class="text-field">Name / Title</div></div>
    <div class="col-md-4"><div class="text-field">Date</div></div>
</div>

<p style="margin-top: 60px">I Certify that the above information is true and correct to the best of my knowledge. I understand that any false statement herein may result in the loss of my Wildlife Rehabilitation Memorandum of Understanding.</p>

<div class="row margin-bottom signature-margin">
    <div class="col-md-6"><div class="text-field">Signature</div></div>
    <div class="col-md-6"><div class="text-field">Date</div></div>
</div>

<p><strong>Send To:</strong><br>
California Department of Fish and Wildlife<br>
Heather Perry<br>
Wildlife Rehabilitation Coordinator<br>
1701 Nimbus Rd.<br>
Rancho Cordova, CA</p>

@stop
