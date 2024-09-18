@extends('report')

@section('body')

<style>
    .row {
        margin-top: 5px;
    }

    .hr {
        border-top: 3px solid black;
        margin: 30px 0;
    }

    label {
        font-weight: 400;
        font-size: 14px;
    }
</style>

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

<h3 class="print-caption">PART A</h3>
<section>
    <div class="row">
        <?php $name = collect(explode(' ', $account->contact_name, 2)); ?>
        <div class="col-md-4">
            {{ $name->get(0) }}
            <div class="text-field-bottom">
                <label>Last</label>
            </div>
        </div>
        <div class="col-md-5">
            {!! $name->get(1, '&nbsp;') !!}
            <div class="text-field-bottom">
                <label>First</label>
            </div>
        </div>
        <div class="col-md-1">
            &nbsp;
            <div class="text-field-bottom">
                <label>M.I.</label>
            </div>
        </div>
        <div class="col-md-2">
            &nbsp;
            <div class="text-field-bottom">
                <label>DOB (mm/dd/yyyy)</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            {!! $account->address ?? '&nbsp;' !!}
            <div class="text-field-bottom">
                <label>Street Address</label>
            </div>
        </div>
        <div class="col-md-2">
            &nbsp;
            <div class="text-field-bottom">
                <label>Apartment/Unit</label>
            </div>
        </div>
        <div class="col-md-5">
            {!! $account->city ?? '&nbsp;' !!}
            <div class="text-field-bottom">
                <label>City</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            &nbsp;
            <div class="text-field-bottom">
                <label>County</label>
            </div>
        </div>
        <div class="col-md-2">
            {!! $account->subdivision ?? '&nbsp;' !!}
            <div class="text-field-bottom">
                <label>State</label>
            </div>
        </div>
        <div class="col-md-5">
            {!! $account->postal_code ?? '&nbsp;' !!}
            <div class="text-field-bottom">
                <label>Zip Code</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            {!! $account->contact_email ?? '&nbsp;' !!}
            <div class="text-field-bottom">
                <label>Email</label>
            </div>
        </div>
        <div class="col-md-6">
            {!! $account->phone_number ?? '&nbsp;' !!}
            <div class="text-field-bottom">
                <label>Telephone</label>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            {!! $account->subdivision_permit_number ?? '&nbsp;' !!}
            <div class="text-field-bottom">
                <label>NYS License Number</label>
            </div>
        </div>
        <div class="col-md-4">
            {!! $account->federal_permit_number ?? '&nbsp;' !!}
            <div class="text-field-bottom">
                <label>Federal Permit Number</label>
            </div>
        </div>
        <div class="col-md-4">
            &nbsp;
            <div class="text-field-bottom">
                <label>Federal Permit Expiration Date</label>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12" style="margin-top: 20px; vertical-align: middle;">
            <p style="float: left; margin-right: 40px; margin-bottom: 0px;">Do you want your name to appear on the statewide list of Wildlife Rehabilitators?</p>
            <div style="float: left">
                <div class="bordered" style="height: 20px; width: 20px; float: left;"></div> <label style="margin: 0 5px">Yes</label>
            </div>
            <div class="bordered" style="height: 20px; width: 20px; float: left;"></div> <label style="margin-left: 5px">No</label>
        </div>
    </div>
</section>

<table class="print-table">
    <caption>PART B</caption>
    <thead>
        <tr>
            <th style="width: 180px;">Species</th>
            <th style="width: 130px;">Age</th>
            <th style="width: 200px;">From</th>
            <th style="width: 150px;">Date Received</th>
            <th style="width: 200px;">Location Found</th>
            <th style="width: auto;">Cause of Distress</th>
            <th style="width: 150px;">Disposition</th>
            <th style="width: 150px;"></th>
            <th style="width: 200px;"></th>
        </tr>
        <tr>
            <th colspan="6"></th>
            <th></th>
            <th>Date</th>
            <th>Location</th>
        </tr>
    </thead>
    @foreach($admissions as $index => $admission)
        <tr>
            <td>{{ $admission->patient->common_name }}</td>
            <td>{{ $admission->patient->intake_exam->full_age }}</td>
            <td>{{ $admission->patient->rescuer->fullName . ', ' . $admission->patient->rescuer->city }}</td>
            <td>{{ $admission->patient->admitted_at_formatted }}</td>
            <td>{!! $admission->patient->location_found !!}</td>
            <td>{{ $admission->patient->predictions->pluck('prediction')->implode(', ') }}</td>
            <td>{{ format_disposition($admission->patient->disposition) }}</td>
            <td>{{ $admission->patient->dispositioned_at_formatted }}</td>
            <td>{!! $admission->patient->disposition_locale !!}</td>
        </tr>
    @endforeach
</table>

<h3 class="print-caption">Comments</h3>
<div class="bordered" style="height: 100px;"></div>
@stop
