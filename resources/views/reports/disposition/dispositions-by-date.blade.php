@extends('report')

@section('body')

<h1>{{ $title }}</h1>
<h6>Between: {{ $dateFrom }} to {{ $dateTo }}</h6>

<h4>Legend</h4>
<p>
    <dl class="dl-inline">
        <dt>R</dt> <dd>Released</dd>
        <dt>T</dt> <dd>Transferred</dd>
        <dt>P</dt> <dd>Pending</dd>
        <dt>E</dt> <dd>Euthanized</dd>
        <dt>D</dt> <dd>Died</dd>
        <dt>DOA</dt> <dd>Dead on Arrival</dd>
    </dl>
</p>

<table class="table">
    <tbody>
        <thead>
            <tr>
                <th style="width:250px">Disposition Date</th>
                <th style="width:auto">Total</th>
                <th style="width:100px">R</th>
                <th style="width:100px">T</th>
                <th style="width:100px">E</th>
                <th style="width:100px">D</th>
                <th style="width:100px">DOA</th>
            </tr>
        </thead>
        @foreach($data as $row)
            <tr>
                <td>{{ $row->dispositioned_at }}</td>
                <td>{{ $row->total }}</td>
                <td>
                    {{ $row->released }}<br>
                    ({{ Number::percentageOf($row->released, $row->total) }}%)
                </td>
                <td>
                    {{ $row->transferred }}<br>
                    ({{ Number::percentageOf($row->transferred, $row->total) }}%)
                </td>
                <td>
                    {{ $row->euthanized }}<br>
                    ({{ Number::percentageOf($row->euthanized, $row->total) }}%)
                </td>
                <td>
                    {{ $row->died }}<br>
                    ({{ Number::percentageOf($row->died, $row->total) }}%)
                </td>
                <td>
                    {{ $row->doa }}<br>
                    ({{ Number::percentageOf($row->doa, $row->total) }}%)
                </td>
            </tr>
        @endforeach
        <tr>
            <th style="text-align:right">Totals</th>
            <td>{{ $data->sum('total') }}</td>
            <td>
                {{ $data->sum('released') }}<br>
                ({{ Number::percentageOf($data->sum('released'), $data->sum('total')) }}%)
            </td>
            <td>
                {{ $data->sum('transferred') }}<br>
                ({{ Number::percentageOf($data->sum('transferred'), $data->sum('total')) }}%)
            </td>
            <td>
                {{ $data->sum('euthanized') }}<br>
                ({{ Number::percentageOf($data->sum('euthanized'), $data->sum('total')) }}%)
            </td>
            <td>
                {{ $data->sum('died') }}<br>
                ({{ Number::percentageOf($data->sum('died'), $data->sum('total')) }}%)
            </td>
            <td>
                {{ $data->sum('doa') }}<br>
                ({{ Number::percentageOf($data->sum('doa'), $data->sum('total')) }}%)
            </td>
        </tr>
    </tbody>
</table>

@stop
