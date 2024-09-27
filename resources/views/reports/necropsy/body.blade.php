
<div class="row">
    <div class="col-md-4">
        <strong>{{ __('Date Admitted') }}:</strong>
        {{ $admission->patient->admitted_at_for_humans }}
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
        {{ $admission->patient->necropsy?->necropsied_at_for_humans }}
    </div>
    <div class="col-md-4">
        <strong>{{ __('Prosector') }}:</strong>
        {{ $admission->patient->necropsy?->prosector }}
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <strong>{{ __('Photos Collected?') }}:</strong>
        {{ $admission->patient->necropsy?->is_photos_collected ? __('Yes') : __('No') }}
    </div>
    <div class="col-md-3">
        <strong>{{ __('Radiographed?') }}:</strong>
        {{ $admission->patient->necropsy?->is_carcass_radiographed ? __('Yes') : __('No') }}
    </div>
</div>

<hr>
<h2>{{ __('Carcass') }}</h2>
<div class="row">
    <div class="col-md-5">
        <strong>{{ __("Condition") }}:</strong>
        {{ $admission->patient->necropsy?->carcass_condition }}
    </div>
    <div class="col-md-3">
        <strong>{{ __('Scavenged?') }}:</strong>
        {{ $admission->patient->necropsy?->is_scavenged ? __('Yes') : __('No') }}
    </div>
</div>
<div class="row">
    <div class="col-md-5">
        <strong>{{ __('Discarded After Necropsy') }}:</strong>
        {{ $admission->patient->necropsy?->is_discarded ? __('Yes') : __('No') }}
    </div>
    <div class="col-md-3">
        <strong>{{ __('Frozen') }}?:</strong>
        {{ $admission->patient->necropsy?->is_frozen ? __('Yes') : __('No') }}
    </div>
</div>

<hr>
<h2>{{ __('Morphometrics') }}</h2>
<div class="row">
    <div class="col-md-2">
        <strong>{{ __('Weight') }}:</strong>
        {{ $admission->patient->necropsy?->weight ? $admission->patient->necropsy?->weight . $admission->patient->necropsy?->weight_unit : '' }}
    </div>
    <div class="col-md-2">
        <strong>{{ __('Sex') }}:</strong>
        {{ $admission->patient->necropsy?->sex }}
    </div>
    <div class="col-md-2">
        <strong>{{ __('Unflat Wing') }}:</strong>
        {{ $admission->patient->necropsy?->wing ? $admission->patient->necropsy?->wing . 'mm' : '' }}
    </div>
    <div class="col-md-3">
        <strong>{{ __('Culmen') }}:</strong>
        {{ $admission->patient->necropsy?->culmen ? $admission->patient->necropsy?->culmen . 'mm' : '' }}
    </div>
    <div class="col-md-3">
        <strong>{{ __('Bill Depth') }}:</strong>
        {{ $admission->patient->necropsy?->bill_depth ? $admission->patient->necropsy?->bill_depth . 'mm' : '' }}
    </div>
</div>
<div class="row">
    <div class="col-md-2">
        <strong>{{ __('Age') }}:</strong>
        {{ intval($admission->patient->necropsy?->age) ? (float)$admission->patient->necropsy?->age : '' }}
        {{ $admission->patient->necropsy?->age_unit }}
    </div>
    <div class="col-md-2">
        <strong>{{ __('Body Condition') }}:</strong>
        {{ $admission->patient->necropsy?->bcs }}
    </div>
    <div class="col-md-2">
        <strong>{{ __('Tarsus') }}:</strong>
        {{ $admission->patient->necropsy?->tarsus ? $admission->patient->necropsy?->tarsus . 'mm' : '' }}
    </div>
    <div class="col-md-3">
        <strong>{{ __('Exposed Culmen') }}:</strong>
        {{ $admission->patient->necropsy?->exposed_culmen ? $admission->patient->necropsy?->exposed_culmen . 'mm' : '' }}
    </div>
</div>

<hr>
<h2>{{ __('Body Systems') }}</h2>

@foreach($necropsyBodyPartOptions as $bodyPart)
    <p>
        <strong>{!! $bodyPart['label'] !!}:</strong>
        <i>{{ $admission->patient->necropsy?->{$bodyPart['value'].'_finding'} }}</i>
        {!! $admission->patient->necropsy?->{$bodyPart['value']} ? ' &mdash; ' . $admission->patient->necropsy?->{$bodyPart['value']} : '' !!}
    </p>
@endforeach

<hr>
<h2>{{ __('Summary') }}</h2>
<p>
    <strong>{{ __('Samples Collected') }}:</strong>
    {{ is_array($admission->patient->necropsy?->samples_collected) ? implode(', ', $admission->patient->necropsy?->samples_collected) : '' }}
</p>
<p>
    <strong>{{ __('Morphologic Diagnosis') }}:</strong>
    {{ $admission->patient->necropsy?->morphologic_diagnosis }}
</p>
<p>
    <strong>{{ __('Gross Summary Diagnosis') }}:</strong>
    {{ $admission->patient->necropsy?->gross_summary_diagnosis }}
</p>
