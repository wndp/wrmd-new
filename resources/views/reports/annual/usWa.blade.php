@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<h6 class="row">
    <div class="col-md-10 label">1. Total Number of Mammals Treated:</div>
    <div class="col-md-2 text-field">{{ $counts->mammals }}</div>
</h2>

<h6 class="row">
    <div class="col-md-10 label">2. Total Number of Birds Treated:</div>
    <div class="col-md-2 text-field">{{ $counts->birds }}</div>
</h2>

<h6 class="row">
    <div class="col-md-10 label">3. Total Number of Reptiles and Amphibians Treated:</div>
    <div class="col-md-2 text-field">{{ $counts->herps }}</div>
</h2>

<h6 class="row">
    <div class="col-md-10 label">4. Total Number of Animals Admitted: (all admissions except DOA but including EOA)</div>
    <div class="col-md-2 text-field">{{ $counts->total }}</div>
</h2>

<h6 class="row">
    <div class="col-md-10 label">5. Total Number of Animals Released:</div>
    <div class="col-md-2 text-field">{{ $counts->released }}</div>
</h2>

<h6 class="row">
    <div class="col-md-10 label">6. Total Number of Animals Transferred:</div>
    <div class="col-md-2 text-field">{{ $counts->transferred }}</div>
</h2>

<h6 class="row">
    <div class="col-md-10 label">7. Number of Animals in your possession held over from last year:</div>
    <div class="col-md-2 text-field">{{ $heldOver }}</div>
</h2>

<h6 class="label">8. Threatened and Endangered Species treated (do not count Bald Eagles or any falcons including Peregrine):</h2>
<table class="table" style="margin-top: 0;">
    <thead>
        <tr>
            <th style="width: auto;">Species</th>
            <th style="width: 130px;">Number</th>
            <th style="width: 200px;">Outcome</th>
            <th style="width: 130px;"># Released</th>
        </tr>
    </thead>
    @foreach($threatened as $admission)
        <tr>
            <td>{{ $admission->patient->common_name }}</td>
            <td>{{ $admission->total }}</td>
            <td>{{ $admission->patient->disposition }}</td>
            <td>{{ $admission->patient->total_released }}</td>
        </tr>
    @endforeach
</table>

<h6 class="label">9. Non-releasable animals held in your possession for Education:</h2>
<table class="table" style="margin-top: 0;">
    <thead>
        <tr>
            <th style="width:auto">Species</th>
            <th style="width:200px">Number</th>
            <th style="width:200px">Year Acquired</th>
        </tr>
    </thead>
    <tbody>
        @foreach($education as $admission)
            <tr>
                <td>{{ $admission->common_name }}</td>
                <td>{{ $admission->total }}</td>
                <td>{{ $year }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<h6 class="label">10. Non-releasable animals held in your possession for Fostering:</h2>
<table class="table" style="margin-top: 0;">
    <thead>
        <tr>
            <th style="width:auto">Species</th>
            <th style="width:200px">Number</th>
            <th style="width:200px">Year Acquired</th>
        </tr>
    </thead>
    <tbody>
        @foreach($nonReleasable as $admission)
            <tr>
                <td>{{ $admission->common_name }}</td>
                <td>{{ $admission->total }}</td>
                <td>{{ $year }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<newpage />

<h6 class="label">11. Changes to your permit:</h2>
<div class="row">
    <div class="col-md-5">The facility address has changed</div>
    <div class="bordered" style="height: 20px; width: 20px; float: left;"></div>
</div>
<div class="row">
    <div class="col-md-5">The facility mailing address has changed</div>
    <div class="bordered" style="height: 20px; width: 20px; float: left;"></div>
</div>
<div class="row">
    <div class="col-md-5">The principle veterinarian has changed</div>
    <div class="bordered" style="height: 20px; width: 20px; float: left;"></div>
</div>

<p style="clear:left; color:#ff6666; margin-top:10mm;">NOTE: The daily ledger for all animals admitted to and treated at your facility for the reporting year must accompany this form in order for your year-end report to be valid.</p>


<div class="row margin-bottom signature-margin">
    <div class="col-md-8"><div class="text-field">Permit Holder's Signature</div></div>
    <div class="col-md-4"><div class="text-field">Date</div></div>
</div>

<p style="clear:left; margin-top:10mm;">Please mail one copy of the completed Annual Report Form and Ledger to the Wildlife Rehabilitation Manager:</p>

<p>Patricia Thompson<br>
16018 Mill Creek Blvd<br>
Mill Creek WA 98012</p>

@stop
