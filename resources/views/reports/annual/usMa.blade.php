@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

@foreach($collection as $class => $classCollection)
    <table class="print-table">
        <caption>{{ $class }}</caption>
        <thead>
            <tr>
                <th style="width: 250px">Species</th>
                <th style="width: 130px">Number Euthanized</th>
                <th style="width: 130px">Number Died</th>
                <th style="width: 130px">Number Released</th>
                <th style="width: 130px">Number Still Held</th>
                <th style="width: 130px">Number Transferred</th>
                <th style="width: 130px">Number DOA</th>
                <th style="width: auto">TOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach($classCollection as $taxaCollection)
                <tr>
                    <td>{{ $taxaCollection->first()->first()->patient->common_name }}</td>
                    <td>{{ $taxaCollection->get('Euthanized', collect())->count() }}</td>
                    <td>{{ $taxaCollection->get('Died', collect())->count() }}</td>
                    <td>{{ $taxaCollection->get('Released', collect())->count() }}</td>
                    <td>{{ $taxaCollection->get('Pending', collect())->count() }}</td>
                    <td>{{ $taxaCollection->get('Transferred', collect())->count() }}</td>
                    <td>{{ $taxaCollection->get('Dead on arrival', collect())->count() }}</td>
                    <td>{{ $taxaCollection->flatten()->count() }}</td>
                </tr>
            @endforeach
            <tr>
                <th>Grand Totals</th>
                <td>{{ $classCollection->pluck('Euthanized')->flatten()->filter()->count() }}</td>
                <td>{{ $classCollection->pluck('Died')->flatten()->filter()->count() }}</td>
                <td>{{ $classCollection->pluck('Released')->flatten()->filter()->count() }}</td>
                <td>{{ $classCollection->pluck('Pending')->flatten()->filter()->count() }}</td>
                <td>{{ $classCollection->pluck('Transferred')->flatten()->filter()->count() }}</td>
                <td>{{ $classCollection->pluck('Dead on arrival')->flatten()->filter()->count() }}</td>
                <td>{{ $classCollection->flatten()->count() }}</td>
            </tr>
        </tbody>
    </table>

    @if($classCollection->pluck('Pending')->flatten()->filter()->count() !== 0)
        <table class="print-table">
            <caption>{{ $class }} - If still held, why?</caption>
            <thead>
                <tr>
                    <th style="width: 150px">Case Number</th>
                    <th style="width: 250px">Species</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($classCollection->pluck('Pending')->flatten()->filter() as $admission)
                    <tr>
                        <td>{{ $admission->case_number }}</td>
                        <td>{{ $admission->patient->common_name }}</td>
                        <td>{{ $admission->patient->reason_for_disposition }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    @if($classCollection->pluck('Transferred')->flatten()->filter()->count() !== 0)
        <table class="print-table">
            <caption>{{ $class }} - If transferred, to whom?</caption>
            <thead>
                <tr>
                    <th style="width: 150px">Case Number</th>
                    <th style="width: 250px">Species</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($classCollection->pluck('Transferred')->flatten()->filter() as $admission)
                    <tr>
                        <td>{{ $admission->case_number }}</td>
                        <td>{{ $admission->patient->common_name }}</td>
                        <td>{{ $admission->patient->disposition_location }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endforeach
@stop
