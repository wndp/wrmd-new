@extends('report')

@section('body')

<h1>{{ $year }} {{ $title }}</h1>

@include('reporting.reports.annual.partials.header')

<table class="table">
    <caption>Part A. Animals Held Over From Last Year.</caption>
    <thead>
        <tr>
            <th rowspan="2">Species<br>(Full Common Name)</th>
            <th rowspan="2">Year Acquired</th>
            <th rowspan="2">Total Number Received</th>
            <th style="text-align: center; border-left:1px solid #dbdbdb; padding-left: 20px" colspan="5">Disposition (enter # of animals by species)</th>
        </tr>
        <tr>
            <th style="width: 135px; border-left:1px solid #dbdbdb; padding-left: 20px">Released</th>
            <th style="width: 135px;">Died</th>
            <th style="width: 135px;">Euthanized</th>
            <th style="width: 135px;">Transferred to another Rehabilitator</th>
            <th style="width: 135px;">Transferred for Educational Use</th>
        </tr>
    </thead>
    @foreach($heldOver as $row)
        <tr>
            <td>{{ $row->common_name }}</td>
            <td style="text-align: center;">{{ $row->year_acquired }}</td>
            <td style="text-align: center;">{{ $row->total }}</td>
            <td style="text-align: center; border-left:1px solid #dbdbdb; padding-left: 20px">{{ $row->released }}</td>
            <td style="text-align: center;">{{ $row->died }}</td>
            <td style="text-align: center;">{{ $row->euthanized }}</td>
            <td style="text-align: center;">{{ $row->transfer_continued_care }}</td>
            <td style="text-align: center;">{{ $row->transfer_education_or_scientific_research_individual + $row->transfer_education_or_scientific_research_institute }}</td>
        </tr>
    @endforeach
</table>

@stop
