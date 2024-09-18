
<div class="row">
    <div class="col-md-4">
        <strong>{{ __('Date Admitted') }}:</strong>
        {{ format_date($admission->patient->admitted_at, settings('date_format')) }}
        {{ format_date($admission->patient->admitted_at, 'H') . ':' . format_date($admission->patient->admitted_at, 'i') }}
    </div>
    <div class="col-md-4">
        <strong>{{ __('Band') }}:</strong>
        {{ $admission->patient->band }}
    </div>
    <div class="col-md-4">
        <strong>{{ __('Name') }}:</strong>
        {{ $admission->patient->name }}
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <strong>{{ __('Reference Number') }}:</strong>
        {{ $admission->patient->reference_number }}
    </div>
    <div class="col-md-4">
        <strong>{{ __('Microchip Number') }}:</strong>
        {{ $admission->patient->microchip_number }}
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <strong>{{ __('Date of Necropsy') }}:</strong>
        {{ format_date($necropsy->necropsied_at, settings('date_format') . ' H:i') }}
    </div>
    <div class="col-md-4">
        <strong>{{ __('Prosector') }}:</strong>
        {{ $necropsy->prosector }}
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <strong>{{ __('Photos Collected?') }}:</strong>
        {{ $necropsy->is_photos_collected ? __('Yes') : __('No') }}
    </div>
    <div class="col-md-3">
        <strong>{{ __('Radiographed?') }}:</strong>
        {{ $necropsy->is_carcass_radiographed ? __('Yes') : __('No') }}
    </div>
</div>

<hr>
<h2>{{ __('Carcass') }}</h2>
<div class="row">
    <div class="col-md-5">
        <strong>{{ __("Condition") }}:</strong>
        {{ $necropsy->carcass_condition }}
    </div>
    <div class="col-md-3">
        <strong>{{ __('Scavenged?') }}:</strong>
        {{ $necropsy->is_scavenged ? __('Yes') : __('No') }}
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <strong>{{ __('Discarded After Necropsy') }}:</strong>
        {{ $necropsy->is_discarded ? __('Yes') : __('No') }}
    </div>
    <div class="col-md-3">
        <strong>{{ __('Frozen') }}?:</strong>
        {{ $necropsy->is_frozen ? __('Yes') : __('No') }}
    </div>
</div>

<hr>
<h2>{{ __('Morphometrics') }}</h2>
<div class="row">
    <div class="col-md-2">
        <strong>{{ __('Weight') }}:</strong>
        {{ $necropsy->weight ? $necropsy->weight . $necropsy->weight_unit : '' }}
    </div>
    <div class="col-md-2">
        <strong>{{ __('Sex') }}:</strong>
        {{ $necropsy->sex }}
    </div>
    <div class="col-md-2">
        <strong>{{ __('Unflat Wing') }}:</strong>
        {{ $necropsy->wing ? $necropsy->wing . 'mm' : '' }}
    </div>
    <div class="col-md-3">
        <strong>{{ __('Culmen') }}:</strong>
        {{ $necropsy->culmen ? $necropsy->culmen . 'mm' : '' }}
    </div>
    <div class="col-md-3">
        <strong>{{ __('Bill Depth') }}:</strong>
        {{ $necropsy->bill_depth ? $necropsy->bill_depth . 'mm' : '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        <strong>{{ __('Age') }}:</strong>
        {{ intval($necropsy->age) ? (float)$necropsy->age : '' }}
        {{ $necropsy->age_unit }}
    </div>
    <div class="col-md-2">
        <strong>{{ __('Body Condition') }}:</strong>
        {{ $necropsy->bcs }}
    </div>
    <div class="col-md-2">
        <strong>{{ __('Tarsus') }}:</strong>
        {{ $necropsy->tarsus ? $necropsy->tarsus . 'mm' : '' }}
    </div>
    <div class="col-md-3">
        <strong>{{ __('Exposed Culmen') }}:</strong>
        {{ $necropsy->exposed_culmen ? $necropsy->exposed_culmen . 'mm' : '' }}
    </div>
</div>

<hr>
<h2>{{ __('Body Systems') }}</h2>

@foreach($necopsyBodyParts as $bodyPart)
    <p>
        <strong>{!! $bodyPart['label'] !!}:</strong>
        <i>{{ $necropsy->{$bodyPart['value'].'_finding'} }}</i>
        {!! $necropsy->{$bodyPart['value']} ? ' &mdash; ' . $necropsy->{$bodyPart['value']} : '' !!}
    </p>
@endforeach

<hr>
<h2>{{ __('Summary') }}</h2>
<p>
    <strong>{{ __('Samples Collected') }}:</strong>
    {{ is_array($necropsy->samples_collected) ? implode(', ', $necropsy->samples_collected) : '' }}
</p>
<p>
    <strong>{{ __('Morphologic Diagnosis') }}:</strong>
    {{ $necropsy->morphologic_diagnosis }}
</p>
<p>
    <strong>{{ __('Gross Summary Diagnosis') }}:</strong>
    {{ $necropsy->gross_summary_diagnosis }}
</p>
