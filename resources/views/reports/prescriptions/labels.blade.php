@extends('report')

@section('body')

@foreach($prescriptions as $index => $prescription)

    @if($index > 0)
        <p style="page-break-before: always">
    @endif

    <?php
    $numberOfDates = is_null($prescription->rx_ended_at)
        ? 9
        : $prescription->rx_started_at->diffInDays($prescription->rx_ended_at) + 1;
    ?>

    <div class="clearfix">
        <strong>Case #</strong> {{ $prescription->patient->admissions($currentAccount->id)->caseNumber }}
        <strong style="margin-left: 20px">Common Name</strong> {{ $prescription->patient->common_name }}
        <strong style="margin-left: 20px">Band</strong> {{ $prescription->patient->band }}
        <strong style="margin-left: 20px">Location</strong> {{ $prescription->patient->currentLocation }}
    </div>

    <hr style="margin-top:0; margin-bottom:0;">

    <div class="clearfix">
        <p style="float: left; width: 50%"><strong>Medication</strong> {{ $prescription->drug }} {{ $prescription->full_concentration }}</p>
        <p style="float: left; width: 25%"><strong>Dose / Route</strong> {{ $prescription->full_dose }} {{ $prescription->route }}</p>
        <p style="float: left; width: 25%"><strong>Frequency</strong> {{ $prescription->frequency }}</p>
    </div>
    <div class="clearfix">
        <p style="float: left; width: 25%"><strong>Duration</strong> {{ $prescription->duration }}</p>
        <p style="float: left; width: 25%"><strong>Dosage</strong> {{ $prescription->full_dosage }}</p>
        <p style="float: left; width: 25%"><strong>Amount dispensed</strong> </p>
        <p style="float: left; width: 25%"><strong>Exp date</strong> </p>
    </div>

    <table class="table table-bordered" style="text-align:center; margin-top:0; margin-bottom:0;">
        <tr>
            <td></td>
            <td colspan="3">Time Given / Initials</td>
            <td></td>
        </tr>
        <tr>
            <td style="width: 150px">Date</td>
            <td style="width: 150px">AM</td>
            <td style="width: 150px">Noon</td>
            <td style="width: 150px">PM</td>
            <td style="width: 400px">Special Instructions</td>
        </tr>
        <tr>
            <td>{{ $prescription->rx_started_at->format(settings('date_format')) }}</td>
            <td></td>
            <td></td>
            <td></td>
            <td rowspan="10">
                {{ $prescription->instructions }}
            </td>
        </tr>
        @foreach(range(1, 9) as $n)
            <tr>
                <td>
                    @if($n < $numberOfDates)
                        @if($prescription->frequency === 'q2d')
                            @if($n % 2 == 0)
                                {{ $prescription->rx_started_at->addDays($n)->format(settings('date_format')) }}
                            @else
                                &nbsp;
                            @endif
                        @elseif($prescription->frequency === 'q3d')
                            @if($n % 3 == 0)
                                {{ $prescription->rx_started_at->addDays($n)->format(settings('date_format')) }}
                            @else
                                &nbsp;
                            @endif
                        @elseif($prescription->frequency === 'q4d')
                            @if($n % 4 == 0)
                                {{ $prescription->rx_started_at->addDays($n)->format(settings('date_format')) }}
                            @else
                                &nbsp;
                            @endif
                        @elseif($prescription->frequency === 'q5d')
                            @if($n % 5 == 0)
                                {{ $prescription->rx_started_at->addDays($n)->format(settings('date_format')) }}
                            @else
                                &nbsp;
                            @endif
                        @elseif($prescription->frequency === 'q7d')
                            @if($n % 7 == 0)
                                {{ $prescription->rx_started_at->addDays($n)->format(settings('date_format')) }}
                            @else
                                &nbsp;
                            @endif
                        @elseif($prescription->frequency === 'q14d')
                            @if($n % 14 == 0)
                                {{ $prescription->rx_started_at->addDays($n)->format(settings('date_format')) }}
                            @else
                                &nbsp;
                            @endif
                        @elseif($prescription->frequency === 'q21d')
                            @if($n % 21 == 0)
                                {{ $prescription->rx_started_at->addDays($n)->format(settings('date_format')) }}
                            @else
                                &nbsp;
                            @endif
                        @elseif($prescription->frequency === 'q28d')
                            @if($n % 28 == 0)
                                {{ $prescription->rx_started_at->addDays($n)->format(settings('date_format')) }}
                            @else
                                &nbsp;
                            @endif
                        @else
                            {{ $prescription->rx_started_at->addDays($n)->format(settings('date_format')) }}
                        @endif
                    @else
                        &nbsp;
                    @endif
                </td>
                <td></td>
                <td></td>
                <td></td>
            </tr>

        @endforeach
    </table>

    @if($prescription->veterinarian)
        <p style="margin-top: 5px">
            {{ $prescription->veterinarian->name }}
            {!! $prescription->veterinarian->full_address !!}
            License# {{ $prescription->veterinarian->license }}
        </p>
    @endif
@endforeach

@stop
