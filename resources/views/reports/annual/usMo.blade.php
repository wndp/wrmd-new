@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <thead>
        <tr>
            <th style="width: 100px">Case Number</th>
            <th style="width: 150px">Species</th>
            <th style="width: 130px">Date Received</th>
            <th style="width: 100px">County of Origin</th>
            <th style="width: 145px">Received From</th>
            <th style="width: 105px">Adult or Juvenile</th>
            <th style="width: 110px">Disposition / Date</th>
            <th style="width: auto">If released, list exact release site (county, Department Area, landowner's name, etc.</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <?php
            $originSource  = empty($admission->patient->rescuer->organization) ? $admission->patient->rescuer->first_name.' '.$admission->patient->rescuer->last_name : $admission->patient->rescuer->organization;
            $originSource .= '<br>' . $admission->patient->rescuer->county;
            ?>
            <tr>
                <td>{{ $admission->caseNumber }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->admitted_at->format($account->settingsStore()->get('date_format')) }}</td>
                <td>{{ $admission->patient->county_found }}</td>
                <td>
                    {!! $originSource !!}
                    {!! $admission->patient->location_found !!}
                </td>
                <td>
                    <?php $exam = $admission->patient->intakeExam; ?>
                    @if(! empty($exam))
                        @if($exam->age_unit === 'Adult')
                            Adult
                        @elseif(empty($exam->age_unit))

                        @else
                            Juvenile
                        @endif
                    @endif
                </td>
                <td>
                    <?php $disposition = format_disposition($admission->patient->disposition);?>
                    {{ ($disposition === 'Pending' ? 'Still in Captivity' : $disposition) }}<br>
                    {{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}
                </td>
                <td>
                    @if(in_array($admission->patient->disposition, ['Released', 'Transferred']))
                        {!! $admission->patient->disposition_locale !!}
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
