@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>
<h2 class="print-sub-title">Where Holding: {{ $whereHolding }}, Due By: {{ $date }}</h2>

@foreach($locations as $location => $cases)
    <table class="print-table mt-10">
        <caption>{{ $location }}</caption>
        <thead>
            <tr>
                <th style="width:100px;">Case</th>
                <th style="width:auto;">Species</th>
                <th style="width:140px;">Band</th>
                <th style="width:60px"></th>
                <th style="width:60px"></th>
                <th style="width:60px"></th>
                <th style="width:60px"></th>
            </tr>
        </thead>

        @foreach($cases as $caseNumber => $collection)
            <tr>
                <th>{{ $caseNumber }}</th>
                <th>{{ $collection[0]->patient->common_name }}</th>
                <th colspan="5">{{ $collection[0]->patient->band }}</th>
            </tr>
            @foreach($collection as $index => $prescription)
                @if($prescription->isDueToday())
                    <tr>
                        <td style="border-top: 0px none;"></td>
                        <td colspan="2" style="border-top: 0px none;">
                            {{ $prescription->fullPrescription }}
                        </td>
                        <td style="border-top: 0px none;">
                            <div class="bordered" style="height:30px; width:30px"></div>
                        </td>
                        <td style="border-top: 0px none;">
                            @if(in_array($prescription->frequency, ['bid', 'tid', 'qid']))
                                <div class="bordered" style="height:30px; width:30px"></div>
                            @endif
                        </td>
                        <td style="border-top: 0px none;">
                            @if(in_array($prescription->frequency, ['tid', 'qid']))
                                <div class="bordered" style="height:30px; width:30px"></div>
                            @endif
                        </td>
                        <td style="border-top: 0px none;">
                            @if($prescription->frequency === 'qid')
                                <div class="bordered" style="height:30px; width:30px"></div>
                            @endif
                        </td>
                    </tr>
                @endif
            @endforeach
        @endforeach

    </table>
@endforeach

@stop
