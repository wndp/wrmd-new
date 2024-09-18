@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>
<h2 class="print-sub-title">Between: {{ $dateFrom }} to {{ $dateTo }}</h2>

@foreach($map as $caption => $collection)
    @if(! empty($collection))
        <table class="print-table mt-10">
            <caption>{{ Str::title(Str::plural($caption)) }}</caption>
            <thead>
                <tr>
                    <th style="width:250px">Common Name</th>
                    <th style="width:auto">Total</th>
                    @foreach ($releaseTypes as $type)
                        <th style="width:auto">{{ $type }}</th>
                    @endforeach
                    <th style="width:auto">Unknown</th>
                </tr>
            </thead>
            <tbody>
                @foreach($collection as $admission)
                    <tr>
                        <td>{{ $admission->common_name }}</td>
                        <td>{{ $admission->total }}</td>
                        @foreach ($releaseTypes as $type)
                            <td>
                                {{ $admission->{Str::snake($type)} }}<br>
                                ({{ Number::percentageOf($admission->{Str::snake($type)}, $admission->total) }}%)
                            </td>
                        @endforeach
                        <td>
                            {{ $admission->unknown }}<br>
                            ({{ Number::percentageOf($admission->unknown, $admission->total) }}%)
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th>Totals</th>
                    <td>{{ $collection->sum('total') }}</td>
                    @foreach ($releaseTypes as $term)
                        <td>
                            {{ $collection->sum(Str::snake($term)) }}<br>
                            ({{ Number::percentageOf($collection->sum(Str::snake($term)), $collection->sum('total')) }}%)
                        </td>
                    @endforeach
                    <td>
                        {{ $collection->sum('unknown') }}<br>
                        ({{ Number::percentageOf($collection->sum('unknown'), $collection->sum('total')) }}%)
                    </td>
                </tr>
            </tbody>
        </table>
    @endif
@endforeach

<table class="print-table">
    <caption>Grand Totals</caption>
    <thead>
        <tr>
            <th style="width:250px"></th>
            <th style="width:auto">Total</th>
            @foreach ($releaseTypes as $type)
                <th style="width:auto">{{ $type }}</th>
            @endforeach
            <th style="width:auto">Unknown</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Totals</th>
            <td>{{ $grand->sum('total') }}</td>
            @foreach ($releaseTypes as $type)
                <td>
                    {{ $grand->sum(Str::snake($type)) }}<br>
                    ({{ Number::percentageOf($grand->sum(Str::snake($type)), $grand->sum('total')) }}%)
                </td>
            @endforeach
            <td>
                {{ $grand->sum('unknown') }}<br>
                ({{ Number::percentageOf($grand->sum('unknown'), $grand->sum('total')) }}%)
            </td>
        </tr>
    </tbody>
</table>

@stop
