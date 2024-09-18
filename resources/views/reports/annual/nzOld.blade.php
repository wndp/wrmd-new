@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>
<h2 class="print-sub-title">Between: {{ $dateFrom }} to {{ $dateTo }}</h2>

@include('reports.annual.partials.header')

<h3 class="print-caption">Dispositions</h3>
<dl class="flex text-black text-base mt-4">
    <dt class="font-bold mr-2">R</dt> <dd class="mr-4">Released</dd>
    <dt class="font-bold mr-2">T</dt> <dd class="mr-4">Transferred</dd>
    <dt class="font-bold mr-2">P</dt> <dd class="mr-4">Pending</dd>
    <dt class="font-bold mr-2">E</dt> <dd class="mr-4">Euthanized</dd>
    <dt class="font-bold mr-2">D</dt> <dd class="mr-4">Died</dd>
    <dt class="font-bold mr-2">DOA</dt> <dd>Dead on Arrival</dd>
</dl>

<table class="print-table">
    <thead>
        <tr>
            <th style="width:auto">Common Name</th>
            <th style="width:75px">Total</th>
            <th style="width:75px">R</th>
            <th style="width:75px">T</th>
            <th style="width:75px">P</th>
            <th style="width:75px">E</th>
            <th style="width:75px">D</th>
            <th style="width:75px">DOA</th>
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
            </tr>
        @endforeach
    </tbody>
</table>

@stop
