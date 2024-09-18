@extends('report')

@section('body')

<h1>{{ $title }}</h1>

@foreach($rechecks as $category => $group)

    @if(!$limitToCategory || $limitToCategory === $category)

        <table class="table table-striped-double">
            <caption>{{ $category }}</caption>
            <thead>
                <tr>
                    <th style="width:60px"></th>
                    <th style="width:100px">Case #</th>
                    <th style="width:auto">Species</th>
                    <th style="width:170px">Band / Tag</th>
                    <th style="width:220px">Location</th>
                </tr>
            </thead>
            <tbody>
                @foreach($group as $patientId)
                    <tr>
                        <td><div style="width:50px;height:50px;border:1px solid #777"></div></td>
                        <td>{{ $patientId->first()->admission->case_number }}</td>
                        <td>{{ $patientId->first()->model->patient->common_name }}</td>
                        <td>{{ $patientId->first()->model->patient->band }}</td>
                        <td>{{ $patientId->first()->model->patient->current_location }}</td>
                    </tr>
                    <td colspan="5">
                        @foreach($patientId as $order)
                            <p>{{ $order->scheduled_at_formated }} &mdash; {{ $order->body }}</p>
                        @endforeach
                    </td>
                @endforeach
            </tbody>
        </table>

    @endif

@endforeach

@stop
