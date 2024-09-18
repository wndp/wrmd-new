@extends('report')

@section('body')

<h1 class="print-title">{{ $title }}</h1>
<h2 class="print-sub-title">Where Holding: {{ $whereHolding }}, Date Moved in Between: {{ $dateFrom }} to {{ $dateTo }}</h2>

@foreach($locations as $location => $accountPatients)
    <table class="print-table mt-10">
        <caption>{{ $location }}</caption>
        <thead>
            <tr>
                <th style="width: 75px">Case #</th>
                <th style="width: 200px">Species</th>
                <th style="width: auto">Identifiers</th>
                <th style="width: 150px">Name</th>
                <th style="width: 150px">Days In Care</th>
                <th style="width: 150px">Date Moved In</th>
            </tr>
        </thead>
        <tbody>
            @foreach($accountPatients as $accountPatient)
                <tr>
                    <td>{{ $accountPatient->caseNumber }}</td>
                    <td>{{ $accountPatient->patient->common_name }}</td>
                    <td>
                        @if($accountPatient->hash) QR Code: {{ $accountPatient->hash }} <br> @endif
                        @if($accountPatient->patient->band) Band: {{ $accountPatient->patient->band }} <br> @endif
                        @if($accountPatient->patient->reference_number) Reference Number: {{ $accountPatient->patient->reference_number }} @endif
                    </td>
                    <td>{{ $accountPatient->patient->name }}</td>
                    <td>{{ $accountPatient->patient->daysInCare }}</td>
                    <td>{{ $accountPatient->patient->locations->first()->moved_in_at->format($account->settingsStore()->get('date_format')) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th>Patients</th>
                <td colspan="5">{{ $accountPatients->count() }}</td>
            </tr>
        </tfoot>
    </table>
@endforeach

@stop
