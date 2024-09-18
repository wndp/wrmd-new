@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<?php
$map = [
    'Amphibians' => $amphibianAcquisitions,
    'Birds' => $birdAcquisitions,
    'Mammals' => $mammalAcquisitions,
    'Reptiles' => $reptileAcquisitions
];
?>

@foreach($map as $caption => $acquisitions)
    <table class="print-table">
        <caption>{{ $caption }}</caption>
        <thead>
            <tr>
                <th style="width: 250px">Common Name</th>
                <th style="width: 65px"># Rehabilitated</th>
                <th style="width: 65px"># Still in possession</th>
                <th style="width: 65px"># Transferred for further rehabilitation</th>
                <th style="width: 65px"># Kept or transferred for exhibiting</th>
                <th style="width: 65px"># Kept as surrogates</th>
                <th style="width: 65px"># Euthanized</th>
                <th style="width: 65px"># Died</th>
                <th style="width: 65px"># Released</th>
            </tr>
        </thead>
        <tbody>
            @foreach($acquisitions as $dispositions)
            <tr>
                <td>{{ $dispositions->common_name }}</td>
                <td>{{ $dispositions->total - $dispositions->doa }}</td>
                <td>{{ $dispositions->pending }}</td>
                <td>{{ $dispositions->care }}</td>
                <td>{{ $dispositions->education }}</td>
                <td>{{ $dispositions->surrogates }}</td>
                <td>{{ $dispositions->euthanized }}</td>
                <td>{{ $dispositions->died }}</td>
                <td>{{ $dispositions->released }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

@endforeach

<p style="page-break-before: always">

<p>Permittees are still responsible for maintaining detailed patient records that include species name, date received, person received from, reason obtained, final disposition, and date of disposition. Records are open to inspection by VDGIF personnel and complete copies may be requested by the Department at any time.</p>

<div class="row margin-bottom signature-margin">
    <div class="col-md-6"><div class="text-field">Signature</div></div>
    <div class="col-md-6"><div class="text-field">Date</div></div>
</div>

<p>RETURN TO: Virginia Department of Game & Inland Fisheries, Permits and Lifetime Licensing Section, P.O. Box 11104, Richmond, VA 23230-1104</p>

@stop
