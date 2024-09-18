@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>

@foreach($map as $caption => $collection)
    @if(! empty($collection))
        <table class="print-table mt-10">
            <caption>{{ Str::title(Str::plural($caption)) }}</caption>
            <thead>
                <tr>
                    <th style="width:250px"></th>
                    <th style="width:auto">Total</th>
                    @foreach ($years as $year)
                        <th style="width:auto">{{ $year }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach($collection as $commonName => $admissions)
                    <tr>
                        <th style="width:250px">{{ $commonName }}</th>
                        <td style="width:auto">{{ $admissions->count() }}</td>
                        @foreach ($years as $year)
                            <td style="width:auto">{{ $admissions->where('case_year', $year)->count() }}</td>
                        @endforeach
                    </tr>
                @endforeach
                <tr>
                    <th class="text-right">Totals</th>
                    <td>{{ $collection->sum(function ($admissions) { return $admissions->count(); }) }}</td>
                    @foreach ($years as $year)
                        <td style="width:auto">{{ $collection->sum(function ($admissions) use ($year) { return $admissions->where('case_year', $year)->count(); }) }}</td>
                    @endforeach
                </tr>
            </tbody>
        </table>
    @endif
@endforeach

<table class="print-table mt-10">
    <caption>Grand Totals</caption>
    <thead>
        <tr>
            <th style="width:250px"></th>
            <th style="width:auto">Total</th>
            @foreach ($years as $year)
                <th style="width:auto">{{ $year }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        <tr>
            <th class="text-right">Totals</th>
            <td>{{ $grand->sum(function ($admissions) { return $admissions->count(); }) }}</td>
            @foreach ($years as $year)
                <td style="width:auto">{{ $grand->sum(function ($admissions) use ($year) { return $admissions->where('case_year', $year)->count(); }) }}</td>
            @endforeach
        </tr>
    </tbody>
</table>

@stop
