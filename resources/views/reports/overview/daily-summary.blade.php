@extends('report')

@section('body')

<h1 class="print-title">
    {{ $title }} for {{ $date->format(config('wrmd.date_format')) }}
</h1>

<table class="print-table mt-20">
    <caption>Patients Admitted</caption>
    <thead>
        <tr>
            <th style="width:100px">Case #</th>
            <th style="width:250px">Common Name</th>
            <th style="width:150px">Band</th>
            <th style="width:auto">Name / Reference #</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admitted as $admission)
            <tr>
                <td>{{ $admission->case_number }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->band }}</td>
                <td>{{ $admission->patient->name }} {{ $admission->patient->reference_number }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <td>{{ $admitted->count() }}</td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<table class="print-table mt-10">
    <caption>Patients Dispositioned</caption>
    <thead>
        <tr>
            <th style="width:100px">Case #</th>
            <th style="width:250px">Common Name</th>
            <th style="width:200px">Date Admitted</th>
            <th style="width:150px">Name / Reference #</th>
            <th style="width:130px">Disposition</th>
            <th style="width:auto">Location</th>
        </tr>
    </thead>
    <tbody>
        @foreach($dispositioned as $admission)
            <tr>
                <td>{{ $admission->case_number }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->admitted_at_formatted }}</td>
                <td>{{ $admission->patient->name }} {{ $admission->patient->reference_number }}</td>
                <td>{{ $admission->patient->disposition }}</td>
                <td>{!! $admission->patient->disposition_locale !!}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <td>{{ $dispositioned->count() }}</td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<table class="print-table mt-10">
    <caption>Patients In Care</caption>
    <thead>
        <tr>
            <th style="width:100px">Case #</th>
            <th style="width:250px">Common Name</th>
            <th style="width:200px">Date Admitted</th>
            <th style="width:150px">Band</th>
            <th style="width:auto">Name / Reference #</th>
        </tr>
    </thead>
    <tbody>
        @foreach($inCare as $admission)
            <tr>
                <td>{{ $admission->case_number }}</td>
                <td>{{ $admission->patient->common_name }}</td>
                <td>{{ $admission->patient->admitted_at_formatted }}</td>
                <td>{{ $admission->patient->band }}</td>
                <td>{{ $admission->patient->name }} {{ $admission->patient->reference_number }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <td>{{ $inCare->count() }}</td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<table class="print-table mt-10">
    <caption>Donations</caption>
    <thead>
        <tr>
            <th style="width:250px">Donor</th>
            <th style="width:130px">Value</th>
            <th style="width:130px">Method</th>
            <th style="width:auto">Comments</th>
        </tr>
    </thead>
    <tbody>
        @foreach($donations as $donation)
            <tr>
                <td>{{ $donation->person->identifier }}</td>
                <td>{{ $donation->value_formatted }}</td>
                <td>{{ $donation->method }}</td>
                <td>{{ $donation->comments }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th>Total</th>
            <td>{{ $donations->count() }}</td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<?php echo implode('', event('report.daily-summary', [$team, $date])) ?>

@stop
