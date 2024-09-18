@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>

@foreach($data as $group)
    <table class="print-table mt-10">
        <caption>{{ $group['category'] }}</caption>
        <thead>
            <tr>
                <th style="width:170px"></th>
                <th style="width:auto">Patient</th>
                <th style="width:245px">Identity</th>
                <th style="width:245px">Location</th>
            </tr>
        </thead>
        <tbody>
            @foreach($group['patients'] as $patient)
                <tr class="border-top border-black">
                    <td></td>
                    <th>{{ $patient['case_number'] }} {{ $patient['common_name'] }}</th>
                    <th>{{ $patient['identity'] }}</th>
                    <th>{{ $patient['enclosure'] }}</th>
                </tr>
                @foreach($patient['tasks'] as $task)
                <tr>
                    <td>
                        @for($i=0; $i<$task['number_of_occurrences']; $i++)
                            <div style="width:30px;height:30px;border:1px solid #777;display:inline-block;margin-right:7px"></div>
                        @endfor
                    </td>
                    <td colspan="3">
                        <p>{{ $task['body'] }}</p>
                    </td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>
@endforeach

@stop
