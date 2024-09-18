@extends('layouts.report')

@section('body')

<h1>{{ $year }} {{ $title }}</h1>

@include('reporting.reports.annual.partials.header')

<h6 class="row">
    <div class="col-md-7 label">Total animals admitted to your facilities for report year:</div>
    <div class="col-md-5 text-field">{{ $admissions->count() }}</div>
</h6>

<h4 class="row">
    <div class="col-md-12">Number of Animals Breakdown by code:</div>
</h4>

<h6 class="row">
    <div class="col-md-7 label">Dead on Arrival (DOA)</div>
    <div class="col-md-5 text-field">{{ $admissions->where('patient.disposition_for_minnesota', 'DOA')->count() }}</div>
    <div class="col-md-7 label">Euthanized upon Admission (EA)</div>
    <div class="col-md-5 text-field">{{ $admissions->where('patient.disposition_for_minnesota', 'EA')->count() }}</div>
    <div class="col-md-7 label">Died (D)</div>
    <div class="col-md-5 text-field">{{ $admissions->where('patient.disposition_for_minnesota', 'D')->count() }}</div>
    <div class="col-md-7 label">Euthanized after Treatment (ET)</div>
    <div class="col-md-5 text-field">{{ $admissions->where('patient.disposition_for_minnesota', 'ET')->count() }}</div>
    <div class="col-md-7 label">Released (R)</div>
    <div class="col-md-5 text-field">{{ $admissions->where('patient.disposition_for_minnesota', 'R')->count() }}</div>
    <div class="col-md-7 label">Transferred to another Rehab Facility (TR)</div>
    <div class="col-md-5 text-field">{{ $admissions->where('patient.disposition_for_minnesota', 'TR')->count() }}</div>
    <div class="col-md-7 label">Transferred to an Education or Zoo Facility (TE)</div>
    <div class="col-md-5 text-field">{{ $admissions->where('patient.disposition_for_minnesota', 'TE')->count() }}</div>
    <div class="col-md-7 label">Pending-held over for next year (P)</div>
    <div class="col-md-5 text-field">{{ $admissions->where('patient.disposition_for_minnesota', 'P')->count() }}</div>
</h6>

<h4 class="row">
    <div class="col-md-12">Breakdown by Animal Type:</div>
</h4>

<h6 class="row">
    <div class="col-md-7 label">Threatened/Endangered Species</div>
    <div class="col-md-5 text-field"></div>
    <div class="col-md-7 label">Not native / exotic species</div>
    <div class="col-md-5 text-field"></div>
    <div class="col-md-7 label" style="margin-top: 20px">Total Mammals</div>
    <div class="col-md-5 text-field" style="margin-top: 20px">{{ $admissions->where('patient.species.class', 'Mammalia')->count() }}</div>
    <div class="col-md-7 label">Deer</div>
    <div class="col-md-5 text-field">{{ $admissions->filter(fn ($a) => \Illuminate\Support\Str::contains($a->patient->common_name, 'deer'))->count() }}</div>
    <div class="col-md-7 label">Bear</div>
    <div class="col-md-5 text-field">{{ $admissions->filter(fn ($a) => \Illuminate\Support\Str::contains($a->patient->common_name, 'bear'))->count() }}</div>
    <div class="col-md-7 label" style="margin-top: 20px">Total Birds</div>
    <div class="col-md-5 text-field" style="margin-top: 20px">{{ $admissions->where('patient.species.class', 'Aves')->count() }}</div>
    <div class="col-md-7 label">Raptors</div>
    <div class="col-md-5 text-field">{{ $admissions->filter(fn ($a) => in_array('Raptor', (array) $a->patient->species->lay_groups) )->count() }}</div>
    <div class="col-md-7 label">Game Birds</div>
    <div class="col-md-5 text-field">{{ $admissions->filter(fn ($a) => in_array('Gamebird', (array) $a->patient->species->lay_groups) )->count() }}</div>
    <div class="col-md-7 label">Song Birds</div>
    <div class="col-md-5 text-field">{{ $admissions->filter(fn ($a) => in_array('Songbird', (array) $a->patient->species->lay_groups) )->count() }}</div>
    <div class="col-md-7 label" style="margin-top: 20px">Turtles</div>
    <div class="col-md-5 text-field" style="margin-top: 20px">{{ $admissions->where('patient.species.order', 'Testudines')->count() }}</div>
    <div class="col-md-7 label">Snakes</div>
    <div class="col-md-5 text-field"></div>
    <div class="col-md-7 label">Lizards</div>
    <div class="col-md-5 text-field"></div>
    <div class="col-md-7 label">Amphibians</div>
    <div class="col-md-5 text-field">{{ $admissions->where('patient.species.class', 'Amphibia')->count() }}</div>
</h6>

@stop
