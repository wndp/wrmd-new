<div class="text-field">
    <div class="row">
        <div class="col-md-6">
            <strong>Case #</strong>
            {{ isset($admission) ? $admission->caseNumber : '' }}
            {{ isset($admission) ? $admission->patient->common_name : '' }}
        </div>
        <div class="col-md-6">
            <strong>Date Admitted</strong>
            {{ isset($admission) ? format_date($admission->patient->admitted_at, settings('date_format')) : '' }}
        </div>
    </div>
</div>

<div class="text-field">
    <div class="row">
        <div class="col-md-6">
            <strong>Band</strong>
            {{ isset($admission) ? $admission->patient->band : '' }}
        </div>
        <div class="col-md-6">
            <strong>Name</strong>
            {{ isset($admission) ? $admission->patient->name : '' }}
        </div>
    </div>
</div>

<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Reasons for Admission</strong>
            {{ isset($admission) ? $admission->patient->reasons_for_admission : '' }}
        </div>
    </div>
</div>
<div class="text-field"></div>

<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Diagnosis</strong>
            {{ isset($admission) ? $admission->patient->diagnosis : '' }}
        </div>
    </div>
</div>
<div class="text-field"></div>
