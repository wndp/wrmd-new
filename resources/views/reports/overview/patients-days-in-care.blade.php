@extends('report')

@section('body')

<h1>{{ $title }}</h1>
<h6>Between: {{ $dateFrom }} to {{ $dateTo }}</h6>

@foreach($admissions as $caption => $collection)
    @if($collection->isNotEmpty())
        <table class="table">
            <caption>{{ Str::title(Str::plural($caption)) }}</caption>
            <thead>
                <tr>
                    <th>{{ __('fields.admissions.case_number') }}</th>
                    <th>{{ __('fields.patients.common_name') }}</th>
                    <th>Days in Care</th>
                </tr>
            </thead>
            @foreach($collection as $admission)
                <tr>
                    <td>{{ $admission->case_number }}</td>
                    <td>{{ $admission->patient->common_name }}</td>
                    <td>{{ number_format($admission->patient->days_in_care) }}</td>
                </tr>
            @endforeach
            <tr>
                <th style="width: 33.33%"></th>
                <th style="text-align: right; width: 33%">Total</th>
                <td style="width: 33.33%">{{ number_format($collection->sum(function ($admission) { return $admission->patient->days_in_care; })) }}</td>
            </tr>
        </table>
    @endif
@endforeach

<table class="table">
    <caption>Grand Total</caption>
    <tr>
        <th style="width: 33.33%"></th>
        <th style="text-align: right; width: 33.33%">Total</th>
        <td style="width: 33.33%">{{ number_format($admissions->sum(function ($collection) { return $collection->sum(function ($admission) { return $admission->patient->days_in_care; }); })) }}</td>
    </tr>
</table>

@stop
