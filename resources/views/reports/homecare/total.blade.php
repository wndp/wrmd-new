@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>

<table class="print-table mt-20">
    <thead>
        <tr>
            <th style="width: 200px">Caregiver</th>
            <th style="width: auto">Hours</th>
        </tr>
    </thead>
    <tbody>
        @foreach($caregivers as $caregiver)
            <tr>
                <td>{{ $caregiver->area }}</td>
                <td>{{ $caregiver->hours_sum }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
