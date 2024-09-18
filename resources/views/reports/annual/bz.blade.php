@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>
<h2 class="print-sub-title">{{ __('For the Month of :month', ['month' => $month]) }}</h2>

<table class="print-table">
    <caption>{{ __('Intake') }}</caption>
    <thead>
        <tr>
        @foreach($intake->headings() as $heading)
            <th>{{ $heading }}</th>
        @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($intake->collection() as $row)
            <tr>
            @foreach($row as $column)
                <td>{{ $column }}</td>
            @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<table class="print-table">
    <caption>{{ __('Transfered Out') }}</caption>
    <thead>
        <tr>
        @foreach($transfers->headings() as $heading)
            <th>{{ $heading }}</th>
        @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($transfers->collection() as $row)
            <tr>
            @foreach($row as $column)
                <td>{{ $column }}</td>
            @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<table class="print-table">
    <caption>{{ __('Deaths') }}</caption>
    <thead>
        <tr>
        @foreach($deaths->headings() as $heading)
            <th>{{ $heading }}</th>
        @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($deaths->collection() as $row)
            <tr>
            @foreach($row as $column)
                <td>{{ $column }}</td>
            @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<table class="print-table">
    <caption>{{ __('Releases') }}</caption>
    <thead>
        <tr>
        @foreach($releases->headings() as $heading)
            <th>{{ $heading }}</th>
        @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($releases->collection() as $row)
            <tr>
            @foreach($row as $column)
                <td>{{ $column }}</td>
            @endforeach
            </tr>
        @endforeach
    </tbody>
</table>

<table class="print-table">
    <caption>{{ __('Inventory as of Report End') }}</caption>
    <thead>
        <tr>
        @foreach($inventory->headings() as $heading)
            <th>{{ $heading }}</th>
        @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($inventory->collection() as $location => $collection)
            <tr>
                <th colspan="7" style="padding-top: 20px">{{ $location }}</th>
            </tr>
            @foreach($collection as $row)
                <tr>
                @foreach($row as $column)
                    <td>{{ $column }}</td>
                @endforeach
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>

@stop
