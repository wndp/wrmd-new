@extends('layouts.report')

@section('body')

<h1>{{ $year }} {{ $title }}</h1>

@include('reporting.reports.annual.partials.header')

<table class="table">
    <caption>Species Held Over From Previous Year</caption>
    <thead>
        <tr>
            <th style="width: 150px;">Intake Date</th>
            <th style="width: 250px;">Species</th>
            <th style="width: auto;">Disposition</th>
            <th style="width: 150px;">Date of Disposition</th>
        </tr>
    </thead>
    @foreach($heldOver as $admission)
        <tr>
            <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>{{ $admission->patient->common_name }}</td>
            <td>{{ $admission->patient->disposition }}</td>
            <td>@if($admission->patient->disposition !== 'Pending') {{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }} @endif</td>
        </tr>
    @endforeach
</table>
<h6 class="row">
    <div class="col-md-3 label">Total # Held Over</div>
    <div class="col-md-2 text-field">{{ $heldOver->count() }}</div>
</h6>

<table class="table">
    <caption>Transfers</caption>
    <thead>
        <tr>
            <th style="width: 150px;">Intake Date</th>
            <th style="width: 250px;">Species</th>
            <th style="width: auto;">Nature of Injury</th>
            <th style="width: 150px;">Transfer Date</th>
            <th style="width: 150px;">Transfer To</th>
        </tr>
    </thead>
    @foreach($transfers as $admission)
        <tr>
            <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>{{ $admission->patient->common_name }}</td>
            <td>{{ $admission->patient->diagnosis }}</td>
            <td>{{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>{{ $admission->patient->disposition_location }}</td>
        </tr>
    @endforeach
</table>
<h6 class="row">
    <div class="col-md-3 label">Total # Transferred</div>
    <div class="col-md-2 text-field">{{ $transfers->count() }}</div>
</h6>

<table class="table">
    <caption>Pending</caption>
    <thead>
        <tr>
            <th style="width: 150px;">Intake Date</th>
            <th style="width: 250px;">Species</th>
            <th style="width: auto;">Nature of Injury</th>
            <th style="width: 150px;">Proposed Disposition</th>
        </tr>
    </thead>
    @foreach($pending as $admission)
        <tr>
            <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>{{ $admission->patient->common_name }}</td>
            <td>{{ $admission->patient->diagnosis }}</td>
            <td></td>
        </tr>
    @endforeach
</table>
<h6 class="row">
    <div class="col-md-3 label">Total # Pending</div>
    <div class="col-md-2 text-field">{{ $pending->count() }}</div>
</h6>

<table class="table">
    <caption>Summary By Species</caption>
    <tbody>
        <thead>
            <tr>
                <th style="width:auto">Species</th>
                <th style="width:75px">Total</th>
                <th style="width:75px">R</th>
                <th style="width:75px">P</th>
                <th style="width:75px">D</th>
                <th style="width:75px">E</th>
                <th style="width:75px">D/E-24</th>
            </tr>
        </thead>
        @foreach($summary as $dispositions)
        <tr>
            <td>{{ $dispositions->common_name }}</td>
            <td>{{ $dispositions->total }}</td>
            <td>{{ $dispositions->released }}</td>
            <td>{{ $dispositions->pending }}</td>
            <td>{{ $dispositions->died_after_24 }}</td>
            <td>{{ $dispositions->euthanized_after_24 }}</td>
            <td>{{ $dispositions->died_in_24 + $dispositions->euthanized_in_24 }}</td>
        </tr>
        @endforeach
        <tr>
            <th>Totals</th>
            <td>{{ $summary->sum('total') }}</td>
            <td>{{ $summary->sum('released') }}</td>
            <td>{{ $summary->sum('pending') }}</td>
            <td>{{ $summary->sum('died_after_24') }}</td>
            <td>{{ $summary->sum('euthanized_after_24') }}</td>
            <td>{{ $summary->sum('died_in_24') + $summary->sum('euthanized_in_24') }}</td>
        </tr>
    </tbody>
</table>

@php $collection = collect(); @endphp
<h6 class="label" style="margin-top: 72px;">Annual Mammal Summary</h6>
<div class="row">
    <div class="col-md-2">Total Intake</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Mammalia', $collection)->sum('total') }}</div>
    <div class="col-md-3">Held over from Prior Year</div>
    <div class="col-md-2 text-field">{{ $heldOverByClass->get('Mammalia', $collection)->count() }}</div>
</div>
<div class="row">
    <div class="col-md-2">Total Released</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Mammalia', $collection)->sum('released') }}</div>
    <div class="col-md-1">Died</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Mammalia', $collection)->sum('died_after_24') }}</div>
    <div class="col-md-2">Euthanized</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Mammalia', $collection)->sum('euthanized_after_24') }}</div>
</div>
<div class="row">
    <div class="col-md-4">Died / Euthanized in 24 Hours</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Mammalia', $collection)->sum('died_in_24') + $summaryByClass->get('Mammalia', $collection)->sum('euthanized_in_24') }}</div>
    <div class="col-md-2">Pending</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Mammalia', $collection)->sum('pending') }}</div>
</div>

<h6 class="label" style="margin-top: 72px;">Annual Bird Summary</h6>
<div class="row">
    <div class="col-md-2">Total Intake</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Aves', $collection)->sum('total') }}</div>
    <div class="col-md-3">Held over from Prior Year</div>
    <div class="col-md-2 text-field">{{ $heldOverByClass->get('Aves', $collection)->count() }}</div>
</div>
<div class="row">
    <div class="col-md-2">Total Released</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Aves', $collection)->sum('released') }}</div>
    <div class="col-md-1">Died</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Aves', $collection)->sum('died_after_24') }}</div>
    <div class="col-md-2">Euthanized</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Aves', $collection)->sum('euthanized_after_24') }}</div>
</div>
<div class="row">
    <div class="col-md-4">Died / Euthanized in 24 Hours</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Aves', $collection)->sum('died_in_24') + $summaryByClass->get('Aves', $collection)->sum('euthanized_in_24') }}</div>
    <div class="col-md-2">Pending</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Aves', $collection)->sum('pending') }}</div>
</div>

<h6 class="label" style="margin-top: 72px;">Annual Reptile / Amphibian Summary</h6>
<div class="row">
    <div class="col-md-2">Total Intake</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Reptilia', $collection)->sum('total') + $summaryByClass->get('Amphibia', $collection)->sum('total') }}</div>
    <div class="col-md-3">Held over from Prior Year</div>
    <div class="col-md-2 text-field">{{ $heldOverByClass->get('Reptilia', $collection)->count() + $heldOverByClass->get('Amphibia', $collection)->count() }}</div>
</div>
<div class="row">
    <div class="col-md-2">Total Released</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Reptilia', $collection)->sum('released') + $summaryByClass->get('Amphibia', $collection)->sum('released') }}</div>
    <div class="col-md-1">Died</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Reptilia', $collection)->sum('died_after_24') + $summaryByClass->get('Amphibia', $collection)->sum('died_after_24') }}</div>
    <div class="col-md-2">Euthanized</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Reptilia', $collection)->sum('euthanized_after_24') + $summaryByClass->get('Amphibia', $collection)->sum('euthanized_after_24') }}</div>
</div>
<div class="row">
    <div class="col-md-4">Died / Euthanized in 24 Hours</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Reptilia', $collection)->sum('died_in_24') + $summaryByClass->get('Reptilia', $collection)->sum('euthanized_in_24') + $summaryByClass->get('Amphibia', $collection)->sum('died_in_24') + $summaryByClass->get('Amphibia', $collection)->sum('euthanized_in_24') }}</div>
    <div class="col-md-2">Pending</div>
    <div class="col-md-2 text-field">{{ $summaryByClass->get('Reptilia', $collection)->sum('pending') + $summaryByClass->get('Amphibia', $collection)->sum('pending') }}</div>
</div>

@stop
