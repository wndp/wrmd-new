@extends('report')

@section('body')

<h1>{{ $title }}</h1>
<h6>Between: {{ $dateFrom }} and {{ $dateTo }}</h6>

<table class="table">
    <thead>
        <tr>
            @foreach($headings as $heading)
                <th>{{ $heading }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $row)
            <tr>
                <td>{!! implode('</td><td>', $row) !!}</td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
