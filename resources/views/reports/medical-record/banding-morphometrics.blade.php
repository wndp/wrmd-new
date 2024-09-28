<p style="page-break-before: always">
<h1>{{ __('Banding and Morphometrics') }}</h1>

<h2>{{ __('Banding') }}</h2>
<div class="row">
    <div class="col-md-6">
        <strong>{{ __('Date') }}</strong>
        {{ $bandings->banded_at_for_humans }}
    </div>
    <div class="col-md-6">
        <strong>{{ __('Status Code') }}</strong>
        {{ $statusCodes[$bandings->status_code] ?? '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <strong>{{ __('Band Number') }}</strong>
        {{ $bandings->band_number }}
    </div>
    <div class="col-md-6">
        <strong>Additional Status Code:</strong>
        {{ $additionalInformationStatusCodes[$bandings->additional_status_code] ?? '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <strong>Age Code:</strong>
        {{ $ageCodes[$bandings->age_code] ?? '' }}
    </div>
    <div class="col-md-6">
        <strong>Band Size:</strong>
        {{ $bandSizes[$bandings->band_size] ?? '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <strong>How Aged:</strong>
        {{ $howAgedCodes[$bandings->how_aged] ?? '' }}
    </div>
    <div class="col-md-6">
        <strong>Band Disposition:</strong>
        {{ $dispositionCodes[$bandings->band_disposition] ?? '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <strong>Sex Code:</strong>
        {{ $sexCodes[$bandings->sex_code] ?? '' }}
    </div>
    <div class="col-md-6">
        <strong>Master Bander ID:</strong>
        {{ $bandings->master_bander_id }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <strong>How Sexed:</strong>
        {{ $howSexedCodes[$bandings->how_sexed] ?? '' }}
    </div>
    <div class="col-md-6">
        <strong>Banded By:</strong>
        {{ $bandings->banded_by ?? '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-6"></div>
    <div class="col-md-6">
        <strong>Location ID:</strong>
        {{ $bandings->location_id ?? '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <strong>Remarks:</strong>
        {{ $bandings->remarks ?? '' }}
    </div>
</div>

@if($bandings->auxiliary_marker)
    <hr>
    <h3>Auxiliary Marker</h3>
    <div class="row">
        <div class="col-md-6">
            <strong>Marker Code:</strong>
            {{ $bandings->auxiliary_marker }}
        </div>
        <div class="col-md-6">
            <strong>Marker Color:</strong>
            {{ $auxillaryColorCodes[$bandings->auxiliary_marker_color] ?? '' }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <strong>Marker Type:</strong>
            {{ $auxillaryMarkerTypeCodes[$bandings->auxiliary_marker_type] ?? '' }}
        </div>
        <div class="col-md-6">
            <strong>Marker Code Color:</strong>
            {{ $auxillaryCodeColors[$bandings->auxiliary_marker_code_color] ?? '' }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <strong>Side of Bird:</strong>
            {{ $auxiliarySideOfBird[$bandings->auxiliary_side_of_bird] ?? '' }}
        </div>
        <div class="col-md-6">
            <strong>Placement on Leg:</strong>
            {{ $auxiliaryPlacementOnLeg[$bandings->auxiliary_placement_on_leg] ?? '' }}
        </div>
    </div>
@endif

@if($bandings->recaptured_at)
    <hr>
    <h3>Recapture</h3>
    <div class="row">
        <div class="col-md-6">
            <strong>Recapture Date:</strong>
            {{ $bandings->recaptured_at_for_humans }}
        </div>
        <div class="col-md-6">
            <strong>Present Condition:</strong>
            {{ $presentConditionCodes[$bandings->present_condition] ?? '' }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <strong>Recapture Disposition:</strong>
            {{ $recaptureDispositionCodes[$bandings->recapture_disposition] ?? '' }}
        </div>
        <div class="col-md-6">
            <strong>How Obtained Present Condition:</strong>
            {{ $howPresentConditionCodes[$bandings->how_present_condition] ?? '' }}
        </div>
    </div>
@endif

<hr>
<h2>Morphometrics</h2>
<div class="row">
    <div class="col-md-12">
        <strong>Date:</strong>
        {{ $morphometrics->measured_at_for_humans }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <strong>Wing Cord:</strong>
        {{ $morphometrics->wing_chord ? $morphometrics->wing_chord . 'mm' : '' }}
    </div>
    <div class="col-md-6">
        <strong>Weight:</strong>
        {{ $morphometrics->weight ? $morphometrics->weight . 'g' : '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <strong>Flat Wing:</strong>
        {{ $morphometrics->flat_wing ? $morphometrics->flat_wing . 'mm' : '' }}
    </div>
    <div class="col-md-6">
        <strong>Bill Width:</strong>
        {{ $morphometrics->bill_width ? $morphometrics->bill_width . 'mm' : '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <strong>Tail Length:</strong>
        {{ $morphometrics->tail_length ? $morphometrics->tail_length . 'mm' : '' }}
    </div>
    <div class="col-md-6">
        <strong>Bill Depth:</strong>
        {{ $morphometrics->bill_depth ? $morphometrics->bill_depth . 'mm' : '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <strong>Tarsus Length:</strong>
        {{ $morphometrics->tarsus_length ? $morphometrics->tarsus_length . 'mm' : '' }}
    </div>
    <div class="col-md-6">
        <strong>Bill Length:</strong>
        {{ $morphometrics->bill_length ? $morphometrics->bill_length . 'mm' : '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <strong>Middle Toe Length:</strong>
        {{ $morphometrics->middle_toe_length ? $morphometrics->middle_toe_length . 'mm' : '' }}
    </div>
    <div class="col-md-6">
        <strong>Head Bill Length:</strong>
        {{ $morphometrics->head_bill_length ? $morphometrics->head_bill_length . 'mm' : '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <strong>Hallux Length:</strong>
        {{ $morphometrics->hallux_length ? $morphometrics->hallux_length . 'mm' : '' }}
    </div>
    <div class="col-md-6">
        <strong>Culmen:</strong>
        {{ $morphometrics->culmen ? $morphometrics->culmen . 'mm' : '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <strong>Toe Pad Length:</strong>
        {{ $morphometrics->toe_pad_length ? $morphometrics->toe_pad_length . 'mm' : '' }}
    </div>
    <div class="col-md-6">
        <strong>Exposed Culmen:</strong>
        {{ $morphometrics->exposed_culmen ? $morphometrics->exposed_culmen . 'mm' : '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <strong>Samples Collected:</strong>
        {{ implode(', ', $morphometrics->samples_collected ?? []) }}
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <strong>Remarks:</strong>
        {{ $morphometrics->remarks }}
    </div>
</div>
