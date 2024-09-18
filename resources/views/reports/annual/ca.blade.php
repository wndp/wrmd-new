@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 200px; text-align: center;" rowspan="2">Common Name of<br>Migratory Bird Received</th>
            <th style="width: 300px; text-align: center;" rowspan="2">Location where migratory<br>bird was found<br>(latitude/longitude, nearest<br>city or town, etc.)</th>
            <th style="text-align: center;" colspan="4">Record Dates<br>(yyyy/mm/dd)</th>
            <th style="width: 300px; text-align: center;" rowspan="2">Name of<br>Facility Transferred to or method of disposal</th>
        </tr>
        <tr>
            <th style="width: 125px;">Obtained</th>
            <th style="width: 125px;">Released</th>
            <th style="width: 125px;">Died</th>
            <th style="width: 125px;">Euthanized</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <tr>
                <td>{{ $admission->patient->common_name }}</td>
                <td>
                    @if($admission->patient->coordinates_found && $admission->patient->coordinates_found->getLat() !== 0.0)
                        {{ $admission->patient->coordinates_found->getLat() }}, {{ $admission->patient->coordinates_found->getLng() }}<br>
                    @endif
                    {{ $admission->patient->location_found }}
                </td>
                <td>{{ format_date($admission->patient->admitted_at, 'Y-m-d') }}</td>
                <td>@if($admission->patient->disposition === 'Released') {{ format_date($admission->patient->dispositioned_at, 'Y-m-d') }} @endif</td>
                <td>@if(\Illuminate\Support\Str::contains($admission->patient->disposition, 'Died')) {{ format_date($admission->patient->dispositioned_at, 'Y-m-d') }} @endif</td>
                <td>@if(\Illuminate\Support\Str::contains($admission->patient->disposition, 'Euthanized')) {{ format_date($admission->patient->dispositioned_at, 'Y-m-d') }} @endif</td>
                <td>{!! $admission->patient->disposition_locale !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
