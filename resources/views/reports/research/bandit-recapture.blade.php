@extends('report')

@section('body')

<h1>{{ $title }}</h1>
<h6>Recaptured Between: {{ $dateFrom }} and {{ $dateTo }}</h6>

<table class="table">
    <thead>
        <tr>
            @foreach($headings as $heading)
                <th>{{ $heading }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($bandings as $banding)
            <tr>
                <td>{!! implode('</td><td>', $banding) !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
