@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>
<h6 class="print-sub-title">{{ $subtitle ?? $team->name }}</h6>

<table class="print-table mt-20">
    <thead>
        <tr>
            @foreach($headings as $heading)
                <th>{{ $heading }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($data as $datum)
            <tr>
                <td>{!! implode('</td><td>', $datum) !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
