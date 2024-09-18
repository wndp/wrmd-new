@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>
<h2 class="print-sub-title">Report For the Period Ending: {{ $reportEnding }}</h2>

@include('reports.annual.partials.header')

<table class="print-table">
    <caption>A. New Wildlife Received</caption>
    <thead>
        <tr>
            <th style="width: 90px;">Intake No.</th>
            <th style="width: 130px;">Date Received</th>
            <th style="width: 130px;">Species</th>
            <th style="width: 150px;">Location Found</th>
            <th style="width: auto;">Injury &amp; Cause</th>
            <th style="width: 110px;">Disposition</th>
            <th style="width: 150px;">Disposition Location</th>
            <th style="width: 130px;">Disposition Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($newWildlife as $admission)
            <tr>
                <td>{{ $admission->caseNumber }}</td>
                <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
                <td>
                    {{ $admission->patient->common_name }}<br>
                    <i>{{ $admission->patient->taxon->binomen }}</i>
                </td>
                <td>{!! $admission->patient->location_found !!}</td>
                <td>
                    {{ $admission->patient->diagnosis }}<br>
                    {{ $admission->patient->reasons_for_admission }}
                </td>
                <td>{{ format_disposition($admission->patient->disposition) }}</td>
                <td>{!! $admission->patient->disposition_locale !!}</td>
                <td>{{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="print-table">
    <caption>B. Held Over From Previous Report Period</caption>
    <thead>
        <tr>
            <th style="width: 90px;">No.</th>
            <th style="width: 130px;">Species</th>
            <th style="width: 130px;">Date Received</th>
            <th style="width: auto;">Describe Injury</th>
            <th style="width: 110px;">Disposition</th>
            <th style="width: 130px;">Disposition Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($oldWildlife as $admission)
            <tr>
                <td>{{ $admission->caseNumber }}</td>
                <td>
                    {{ $admission->patient->common_name }}<br>
                    <i>{{ $admission->patient->taxon->binomen }}</i>
                </td>
                <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
                <td>{{ $admission->patient->diagnosis }}</td>
                <td>{{ format_disposition($admission->patient->disposition) }}</td>
                <td>{{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="print-table">
    <caption>C. Transfers</caption>
    <thead>
        <tr>
            <th style="width: 130px;">Species</th>
            <th style="width: 150px;">Transferred to</th>
            <th style="width: 130px;">Date</th>
            <th style="width: auto;">Purpose of Transfer</th>
            <th style="width: 50mm;">ODFW Rep</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transfers as $admission)
            <tr>
                <td>
                    {{ $admission->patient->common_name }}<br>
                    <i>{{ $admission->patient->taxon->binomen }}</i>
                </td>
                <td>{!! $admission->patient->disposition_locale !!}</td>
                <td>{{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
                <td>{{ $admission->patient->transfer_type }}</td>
                <td></td>
            </tr>
        @endforeach
    </tbody>
</table>

<table class="print-table">
    <caption>D. Prohibited Species</caption>
    <thead>
        <tr>
            <th style="width: 130px;">Species</th>
            <th style="width: 130px;">Date Admitted</th>
            <th style="width: 110px;">Disposition</th>
            <th style="width: 130px;">Disposition Date</th>
            <th style="width: auto;">Name &amp; Date of Transferred</th>
        </tr>
    </thead>
    <tbody>
        @foreach($prohibited as $admission)
        <tr>
            <td>
                {{ $admission->patient->common_name }}<br>
                <i>{{ $admission->patient->taxon->binomen}}</i>
            </td>
            <td>{{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>{{ format_disposition($admission->patient->disposition) }}</td>
            <td>{{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}</td>
            <td>{!! $admission->patient->disposition_locale.' '.format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) !!}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="print-table">
    <caption>E. Continuing Education Completed Since Last Application</caption>
    <thead>
        <tr>
            <td style="width: auto;">Conference/Class/Workshop/Webinar, etc</td>
            <td style="width: 130px;">Date Attended</td>
            <td style="width: 100px;"># of hours</td>
            <td style="width: 250px;">Facilitator/Trainer/Teacher</td>
            <td style="width: 250px;">Location</td>
        </tr>
    </thead>
    <tbody>
        <?php for($i=0; $i<10; $i++) { ?>
        <tr>
            <td class="text-field"></td>
            <td class="text-field"></td>
            <td class="text-field"></td>
            <td class="text-field"></td>
            <td class="text-field"></td>
        <?php } ?>
    </tr>
</table>

<table class="print-table">
    <caption>F. Work Under Another Active Licensed Rehabber?</caption>
    <tr>
        <td class="text-field">
            <span style="width: 50px; display: inline-block;"></span> Yes <span style="width: 50px; display: inline-block;"></span> No. If yes, the licensed rehabber must complete & sign the portion below.
        </td>
    </tr>
    <tr>
        <td class="text-field">
            Name: <span style="width: 200px; display: inline-block;"></span> Permit Number: <span style="width: 200px; display: inline-block;"></span>
        </td>
    </tr>
    <tr>
        <td class="text-field">You must have received and rehabilitated wildlife at your facility during the last 180 day report period to qualify as an active rehabber.</td>
    </tr>
    <tr>
        <td class="text-field">Number of hours of work done in the last 180 days:</td>
    </tr>
    <tr>
        <td class="text-field">Description of duties performed:</td>
    </tr>
    <tr>
        <td class="text-field"></td>
    </tr>
    <tr>
        <td class="text-field"></td>
    </tr>
    <tr>
        <td class="text-field"></td>
    </tr>
    <tr>
        <td class="text-field"></td>
    </tr>
    <tr>
        <td class="text-field"></td>
    </tr>
    <tr>
        <td class="text-field"></td>
    </tr>
    <tr>
        <td class="text-field"></td>
    </tr>
    <tr>
        <td class="text-field">I certify that the information I have provided is true and accurate: (signature)</td>
    </tr>
</table>

@stop
