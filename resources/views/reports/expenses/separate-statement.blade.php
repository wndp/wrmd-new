@extends('report')

@section('body')

<h1>{{ $title }}</h1>

<h4>Case# {{ $admission->caseNumber }} </h4>
<p><strong>Species</strong> {{ $admission->patient->common_name }}</p>
<p><strong>Address</strong> {{ $account->mailing_address }}</p>
<p><strong>Date</strong> {{ now(settings('timezone'))->format(settings('date_format').' g:i a') }}</p>

@include('reports.expenses.statement')

@stop
