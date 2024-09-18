@extends('layouts.report')

@section('body')

<h1>{{ $year }} {{ $title }}</h1>

@include('reporting.reports.annual.partials.header')

@foreach($collection as $class => $classCollection)
    <table class="table">
        <caption>{{ $class }}</caption>
        <thead>
            <tr>
                <th style="width: 250px">Species</th>
                <th style="width: 130px">Source(s) of Species</th>
            </tr>
        </thead>
        @foreach($classCollection as $taxaCollection)
            <tr>
                <td>{{ $taxaCollection->first()->first()->patient->common_name }}</td>
                <td>
                    @foreach($taxaCollection as $entity => $entityCollection)
                        <div>{{ $entity ?: 'Unspecified' }}: {{ $entityCollection->count() }}</div>
                    @endforeach
                </td>
            </tr>
        @endforeach
    </table>
@endforeach
@stop
