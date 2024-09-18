@extends('report')

@section('body')

<h1>{{ $title }}</h1>

<h2>Case#: {{ $admission->caseNumber }} <span style="margin-left:50px">Species: {{ $admission->patient->common_name }}</span></h2>

@include('reports.necropsy.body')

@stop
