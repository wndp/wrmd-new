@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>
<h2 class="print-sub-title">Between: {{ $dateFrom }} to {{ $dateTo }}, Grouped By: {{ $groupedBy }}</h2>

@foreach($admissions as $city => $collection)
    <table class="print-table mt-10">
        <caption>{{ $city }}</caption>
        <thead>
            <tr>
                <th>{{ __('Common Name') }}</th>
                <th class="w-full">{{ __('Number of Admissions') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($collection as $commonName => $commonNameCollection)
                <tr>
                    <td>{{ $commonName }}</td>
                    <td>{{ $commonNameCollection->count() }}</td>
                </tr>
            @endforeach
            <tr>
                <th>{{ __('Total') }}</th>
                <td>{{ $collection->sum(fn ($commonNameCollection) => $commonNameCollection->count() ) }}</td>
            </tr>
        </tbody>
    </table>
@endforeach

<table class="print-table mt-10">
    <caption>{{ __('Grand Total') }}</caption>
    <tbody>
        <tr>
            <th class="text-right">{{ __('Total') }}</th>
            <td class="w-full">{{ $admissions->sum(function ($collection) { return $collection->sum(function ($commonNameCollection) { return $commonNameCollection->count(); }); }) }}</td>
        </tr>
    </tbody>
</table>

@stop
