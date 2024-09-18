@extends('report')

@section('body')

<h1>{{ $title }}</h1>

<div class="row" style="line-height: 25px; font-size: 16px; margin-top: 50px;">
    <div class="col-md-6">
        @include('reports.paperforms.cagecard')
    </div>
    <div class="col-md-6" style="border-left: 1px dashed #9b9b9b;">
        @include('reports.paperforms.cagecard')
    </div>
</div>

<hr style="margin: 40px 0px; border-style: dashed;">

<div class="row" style="line-height: 25px; font-size: 16px;">
    <div class="col-md-6">
        @include('reports.paperforms.cagecard')
    </div>
    <div class="col-md-6" style="border-left: 1px dashed #9b9b9b;">
        @include('reports.paperforms.cagecard')
    </div>
</div>

<hr style="margin: 40px 0px; border-style: dashed;">

<div class="row" style="line-height: 25px; font-size: 16px;">
    <div class="col-md-6">
        @include('reports.paperforms.cagecard')
    </div>
    <div class="col-md-6" style="border-left: 1px dashed #9b9b9b;">
        @include('reports.paperforms.cagecard')
    </div>
</div>

<hr style="margin: 40px 0px; border-style: dashed;">

<div class="row" style="line-height: 25px; font-size: 16px;">
    <div class="col-md-6">
        @include('reports.paperforms.cagecard')
    </div>
    <div class="col-md-6" style="border-left: 1px dashed #9b9b9b;">
        @include('reports.paperforms.cagecard')
    </div>
</div>

<hr style="margin: 40px 0px; border-style: dashed;">

<div class="row" style="line-height: 25px; font-size: 16px;">
    <div class="col-md-6">
        @include('reports.paperforms.cagecard')
    </div>
    <div class="col-md-6" style="border-left: 1px dashed #9b9b9b;">
        @include('reports.paperforms.cagecard')
    </div>
</div>

@stop
