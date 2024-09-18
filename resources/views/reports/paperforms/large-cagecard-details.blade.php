<h2 style="padding: 8px 0;">Cage Card</h2>

<div class="bordered" style="padding: 10px;">
    <h4>{{ isset($admission) ? $admission->patient->common_name : '' }} {{ isset($admission) ? $admission->caseNumber : '' }}</h4>
    <div class="text-field">
        <div class="row">
            <div class="col-md-6">
                <strong>Band:</strong>
                {{ isset($admission) ? $admission->patient->band : '' }}
            </div>
            <div class="col-md-6">
                <strong>Name:</strong>
                {{ isset($admission) ? $admission->patient->name : '' }}
            </div>
        </div>
    </div>
    <div class="text-field">
        <div class="row">
            <div class="col-md-6">
                <strong>Reference #:</strong>
                {{ isset($admission) ? $admission->patient->reference_number : '' }}
            </div>
            <div class="col-md-6">
                <strong>Microchip #:</strong>
                {{ isset($admission) ? $admission->patient->microchip_number : '' }}
            </div>
        </div>
    </div>
    <div class="text-field">
        <div class="row">
            <div class="col-md-6">
                <strong>Date Admitted:</strong>
                {{ isset($admission) ? format_date($admission->patient->admitted_at, settings('date_format')) : '' }}
            </div>
            <div class="col-md-6">
                <strong>Date Found:</strong>
                {{ isset($admission) ? format_date($admission->patient->found_at, settings('date_format')) : '' }}
            </div>
        </div>
    </div>
    <div class="text-field" style="border-bottom: none">
        <div class="row">
            <div class="col-md-12">
                <strong>Location Found:</strong>
                {!! isset($admission) ? $admission->patient->location_found : '' !!}
            </div>
        </div>
    </div>
</div>

<div class="bordered" style="padding: 10px; margin-top:20px">
    <div class="text-fiel">
        <div class="row">
            <div class="col-md-8">
                <strong>Current Location:</strong>
                {{ isset($admission) ? $admission->patient->current_location : '' }}
            </div>
            <div class="col-md-4">
                {{ isset($admission) ? $admission->patient->locations->first(null, new \App\Domain\Patients\PatientLocation)->moved_in_at_formatted : '' }}
            </div>
        </div>
    </div>
</div>

<?php $exam = isset($admission) ? $admission->patient->intakeExam : false;?>
<div class="bordered" style="padding: 10px; margin-top:20px">
    <div class="text-field">
        <div class="row">
            <div class="col-md-12">
                <strong>Reason For Admission:</strong>
                {{ isset($admission) ? $admission->patient->reasons_for_admission : '' }}
            </div>
        </div>
    </div>
    <div class="text-field">
        <div class="row">
            <div class="col-md-12">
                <strong>Care by Rescuer:</strong>
                {{ isset($admission) ? $admission->patient->care_by_rescuer : '' }}
            </div>
        </div>
    </div>
    <div class="text-field">
        <div class="row">
            <div class="col-md-12">
                <strong>Notes About Rescue:</strong>
                {{ isset($admission) ? $admission->patient->notes_about_rescue : '' }}
            </div>
        </div>
    </div>
    <div class="text-field">
        <div class="row">
            <div class="col-md-6">
                <strong>Age:</strong>
                {{ isset($admission) && $exam ? $exam->fullAge : '' }}
            </div>
            <div class="col-md-6">
                <strong>Sex:</strong>
                {{ isset($admission) && $exam ? $exam->sex : '' }}
            </div>
        </div>
    </div>
    <div class="text-field">
        <div class="row">
            <div class="col-md-6">
                <strong>Weight:</strong>
                {{ isset($admission) && $exam ? $exam->fullWeight : '' }}
            </div>
            <div class="col-md-6">
                <strong>Temp:</strong>
                {{ isset($admission) && $exam ? $exam->fullTemperature : '' }}
            </div>
        </div>
    </div>
    <div class="text-field">
        <div class="row">
            <div class="col-md-6">
                <strong>Dehydration:</strong>
                {{ isset($admission) && $exam ? $exam->dehydration : '' }}
            </div>
            <div class="col-md-6">
                <strong>Attitude:</strong>
                {{ isset($admission) && $exam ? $exam->attitude : '' }}
            </div>
        </div>
    </div>
    <div class="text-field">
        <div class="row">
            <div class="col-md-6">
                <strong>Body Condition:</strong>
                {{ isset($admission) && $exam ? $exam->bcs : '' }}
            </div>
            <div class="col-md-6">
                <strong>Mucous Membrane:</strong>
                {{ isset($admission) && $exam ? "$exam->mm_color $exam->mm_texture" : '' }}
            </div>
        </div>
    </div>
    <div class="text-field" style="border-bottom: none">
        <div class="row">
            <div class="col-md-12">
                <strong>Initially Examined By:</strong>
                {{ isset($admission) && $exam ? $exam->examiner : '' }}
            </div>
        </div>
    </div>
</div>

<div class="bordered" style="padding: 10px; margin-top:20px">
    <div class="text-field">
        <div class="row">
            <div class="col-md-12">
                <strong>Diagnosis:</strong>
                {{ isset($admission) ? $admission->patient->diagnosis : '' }}
            </div>
        </div>
    </div>
</div>
