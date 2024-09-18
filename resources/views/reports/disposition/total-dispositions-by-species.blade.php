@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>
<h2 class="print-sub-title">Between: {{ $dateFrom }} to {{ $dateTo }}</h2>

<h3 class="print-caption mt-20">Disposition Codes</h3>
<dl class="print-text flex mt-1">
    <dt class="font-bold mr-2">R</dt> <dd class="mr-4">Released</dd>
    <dt class="font-bold mr-2">T</dt> <dd class="mr-4">Transferred</dd>
    <dt class="font-bold mr-2">P</dt> <dd class="mr-4">Pending</dd>
    <dt class="font-bold mr-2">E</dt> <dd class="mr-4">Euthanized</dd>
    <dt class="font-bold mr-2">D</dt> <dd class="mr-4">Died</dd>
    <dt class="font-bold mr-2">DOA</dt> <dd>Dead on Arrival</dd>
</dl>
<p class="print-text mt-10">
    <strong class="font-bold">Survival Rate Including First 24 Hours</strong>
    <br>(Pending + Released + Transferred) / (Total cases - Dead on arrival)
</p>
<p class="print-text mt-10">
    <strong class="font-bold">Survival Rate After First 24 Hours</strong>
    <br>(Pending + Released + Transferred) / (Total cases - Dead on arrival - Died in 24hr - Euthanized in 24hr)
</p>

@foreach($collections as $caption => $acquisitions)
    @if(! empty($acquisitions))
    <table class="print-table mt-10">
        <caption>{{ $caption }}</caption>
        <thead>
            <tr>
                <th>Common Name</th>
                <th>Total</th>
                <th>R</th>
                <th>T</th>
                <th>P</th>
                <th>E</th>
                <th>E +24hr</th>
                <th>D</th>
                <th>D +24hr</th>
                <th>DOA</th>
                <th>Survival Rate Including</th>
                <th>Survival Rate After</th>
            </tr>
        </thead>
        <tbody>
            @foreach($acquisitions as $dispositions)
            <tr>
                <td>{{ $dispositions->common_name }}</td>
                <td>{{ $dispositions->total }}</td>
                <td>
                    {{ $dispositions->released }}<br>
                    ({{ Number::percentageOf($dispositions->released, $dispositions->total) }}%)
                </td>
                <td>
                    {{ $dispositions->transferred }}<br>
                    ({{ Number::percentageOf($dispositions->transferred, $dispositions->total) }}%)
                </td>
                <td>
                    {{ $dispositions->pending }}<br>
                    ({{ Number::percentageOf($dispositions->pending, $dispositions->total) }}%)
                </td>
                <td>
                    {{ $dispositions->euthanized_in_24 }}<br>
                    ({{ Number::percentageOf($dispositions->euthanized_in_24, $dispositions->total) }}%)
                </td>
                <td>
                    {{ $dispositions->euthanized_after_24 }}<br>
                    ({{ Number::percentageOf($dispositions->euthanized_after_24, $dispositions->total) }}%)
                </td>
                <td>
                    {{ $dispositions->died_in_24 }}<br>
                    ({{ Number::percentageOf($dispositions->died_in_24, $dispositions->total) }}%)
                </td>
                <td>
                    {{ $dispositions->died_after_24 }}<br>
                    ({{ Number::percentageOf($dispositions->died_after_24, $dispositions->total) }}%)
                </td>
                <td>
                    {{ $dispositions->doa }}<br>
                    ({{ Number::percentageOf($dispositions->doa, $dispositions->total) }}%)
                </td>
                <td>{{ $dispositions->survival_rate }}</td>
                <td>{{ $dispositions->survival_rate_after }}</td>
            </tr>
            @endforeach
            <tr>
                <th>Totals</th>
                <td>{{ $acquisitions->sum('total') }}</td>
                <td>
                    {{ $acquisitions->sum('released') }}<br>
                    ({{ Number::percentageOf($acquisitions->sum('released'), $acquisitions->sum('total')) }}%)
                </td>
                <td>
                    {{ $acquisitions->sum('transferred') }}<br>
                    ({{ Number::percentageOf($acquisitions->sum('transferred'), $acquisitions->sum('total')) }}%)
                </td>
                <td>
                    {{ $acquisitions->sum('pending') }}<br>
                    ({{ Number::percentageOf($acquisitions->sum('pending'), $acquisitions->sum('total')) }}%)
                </td>
                <td>
                    {{ $acquisitions->sum('euthanized_in_24') }}<br>
                    ({{ Number::percentageOf($acquisitions->sum('euthanized_in_24'), $acquisitions->sum('total')) }}%)
                </td>
                <td>
                    {{ $acquisitions->sum('euthanized_after_24') }}<br>
                    ({{ Number::percentageOf($acquisitions->sum('euthanized_after_24'), $acquisitions->sum('total')) }}%)
                </td>
                <td>
                    {{ $acquisitions->sum('died_in_24') }}<br>
                    ({{ Number::percentageOf($acquisitions->sum('died_in_24'), $acquisitions->sum('total')) }}%)
                </td>
                <td>
                    {{ $acquisitions->sum('died_after_24') }}<br>
                    ({{ Number::percentageOf($acquisitions->sum('died_after_24'), $acquisitions->sum('total')) }}%)
                </td>
                <td>
                    {{ $acquisitions->sum('doa') }}<br>
                    ({{ Number::percentageOf($acquisitions->sum('doa'), $acquisitions->sum('total')) }}%)
                </td>
                <td>{{ Number::survivalRate($acquisitions) }}</td>
                <td>{{ Number::survivalRate($acquisitions, true) }}</td>
            </tr>
        </tbody>
    </table>
    @endif
@endforeach

<table class="print-table mt-10">
    <caption>Grand Totals</caption>
    <thead>
        <tr>
            <th></th>
            <th>Total</th>
            <th>R</th>
            <th>T</th>
            <th>P</th>
            <th>E</th>
            <th>E +24hr</th>
            <th>D</th>
            <th>D +24hr</th>
            <th>DOA</th>
            <th>Survival Rate Including</th>
            <th>Survival Rate After</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <th>Totals</th>
            <td>{{ $grand->sum('total') }}</td>
            <td>
                {{ $grand->sum('released') }}<br>
                ({{ Number::percentageOf($grand->sum('released'), $grand->sum('total')) }}%)
            </td>
            <td>
                {{ $grand->sum('transferred') }}<br>
                ({{ Number::percentageOf($grand->sum('transferred'), $grand->sum('total')) }}%)
            </td>
            <td>
                {{ $grand->sum('pending') }}<br>
                ({{ Number::percentageOf($grand->sum('pending'), $grand->sum('total')) }}%)
            </td>
            <td>
                {{ $grand->sum('euthanized_in_24') }}<br>
                ({{ Number::percentageOf($grand->sum('euthanized_in_24'), $grand->sum('total')) }}%)
            </td>
            <td>
                {{ $grand->sum('euthanized_after_24') }}<br>
                ({{ Number::percentageOf($grand->sum('euthanized_after_24'), $grand->sum('total')) }}%)
            </td>
            <td>
                {{ $grand->sum('died_in_24') }}<br>
                ({{ Number::percentageOf($grand->sum('died_in_24'), $grand->sum('total')) }}%)
            </td>
            <td>
                {{ $grand->sum('died_after_24') }}<br>
                ({{ Number::percentageOf($grand->sum('died_after_24'), $grand->sum('total')) }}%)
            </td>
            <td>
                {{ $grand->sum('doa') }}<br>
                ({{ Number::percentageOf($grand->sum('doa'), $grand->sum('total')) }}%)
            </td>
            <td>{{ Number::survivalRate($grand) }}</td>
            <td>{{ Number::survivalRate($grand, true) }}</td>
        </tr>
    </tbody>
</table>

@stop
