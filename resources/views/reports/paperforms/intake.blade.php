@extends('report')

@section('body')
<?php use \App\Domain\Patients\ExamOptions ?>

<h1>{{ $title }}</h1>
<h2>Intake</h2>

<div class="text-field">
    <div class="row">
        <div class="col-md-3">
            <strong>Case#</strong>
            {{ isset($admission) ? $admission->caseNumber : '' }}
        </div>
        <div class="col-md-5">
            <strong>Species</strong>
            {{ isset($admission) ? $admission->patient->common_name : '' }}
        </div>
        <div class="col-md-4">
            <strong>Date Admitted</strong>
            {{ isset($admission) ? format_date($admission->patient->admitted_at, settings('date_format')) : '' }}
        </div>
    </div>
</div>

<div class="text-field">
    <div class="row">
        <div class="col-md-3">
            <strong>Band</strong>
            {{ isset($admission) ? $admission->patient->band : '' }}
        </div>
        <div class="col-md-5">
            <strong>Reference#</strong>
            {{ isset($admission) ? $admission->patient->reference_number : '' }}
        </div>
        <div class="col-md-4">
            <strong>Name</strong>
            {{ isset($admission) ? $admission->patient->name : '' }}
        </div>
    </div>
</div>

<div class="text-field">
    <div class="row">
        <div class="col-md-6">
            <strong>Date Found</strong>
            {{ isset($admission) ? format_date($admission->patient->found_at, settings('date_format')) : '' }}
        </div>
        <div class="col-md-6">
            <strong>Criminal Activity?</strong>
            @if(isset($admission) and $admission->patient->is_criminal_activity) Yes @else Yes / No @endif
        </div>
    </div>
</div>

<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Location Found</strong>
            {!! isset($admission) ? $admission->patient->location_found : '' !!}
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

<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Care by Rescuer</strong>
            {{ isset($admission) ? $admission->patient->care_by_rescuer : '' }}
        </div>
    </div>
</div>

<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>{{ trans('fields.patients.notes_about_rescue') }}</strong>
            {{ isset($admission) ? $admission->patient->notes_about_rescue : '' }}
        </div>
    </div>
</div>

<div class="text-field">
    <div class="row">
        <div class="col-md-6">
            <strong>Rescuers Name</strong>
            {{ isset($admission) ? $admission->patient->rescuer->fullName : '' }}
        </div>
        <div class="col-md-6">
            <strong>Rescuers Phone</strong>
            {{ isset($admission) ? $admission->patient->rescuer->phone : '' }}
        </div>
    </div>
</div>

<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Rescuers Address</strong>
            {!! isset($admission) ? $admission->patient->rescuer->full_address : '' !!}
        </div>
    </div>
</div>

<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Notes About Rescuer</strong>
            {{ isset($admission) ? $admission->patient->rescuer->notes : '' }}
        </div>
    </div>
</div>

<h2>Initial Exam</h2>
<?php $exam = isset($admission) ? $admission->patient->intakeExam : false; ?>

<div class="row">
    <div class="col-md-8">

        @foreach(ExamOptions::$bodyParts as $bodyPart)
            <div class="text-field">
                <div class="row">
                    <div class="col-md-12">
                        <strong>{{ trans('fields.exams.'.$bodyPart) }}</strong>
                        {{ isset($admission) && $exam ? $exam->{$bodyPart} : '' }}
                    </div>
                </div>
            </div>
            <div class="text-field"></div>
        @endforeach

        <div class="text-field">
            <div class="row">
                <div class="col-md-12">
                    <strong>Diagnosis</strong>
                    {{ isset($admission) ? $admission->patient->diagnosis : '' }}
                </div>
            </div>
        </div>
        <div class="text-field"></div>

    </div>
    <div class="col-md-4">
        <div class="bordered" style="padding: 10px;">
            <div class="text-field">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Age</strong>
                        {{ isset($admission) && $exam ? $exam->fullAge : '' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Sex</strong>
                        {{ isset($admission) && $exam ? $exam->sex : '' }}
                    </div>
                </div>
            </div>
            <div class="text-field">
                <div class="row">
                    <div class="col-md-6">
                        <strong>Weight</strong>
                        {{ isset($admission) && $exam ? $exam->fullWeight : '' }}
                    </div>
                    <div class="col-md-6">
                        <strong>Temp</strong>
                        {{ isset($admission) && $exam ? $exam->fullTemperature : '' }}
                    </div>
                </div>
            </div>
            <div class="bordered-bottom">
                <div class="row">
                    <strong class="col-md-6">Dehydration</strong>
                    <div class="col-md-6">
                        @if(isset($admission) && $exam)
                            {{ $exam->dehydration }}
                        @else
                            <ul class="list-unstyled">
                                @foreach(ExamOptions::$dehydrations as $dehydration)
                                    <li>{{ $dehydration }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bordered-bottom">
                <div class="row">
                    <strong class="col-md-6">Attitude</strong>
                    <div class="col-md-6">
                        @if(isset($admission) && $exam)
                            {{ $exam->attitude }}
                        @else
                            <ul class="list-unstyled">
                                @foreach(ExamOptions::$attitudes as $attitude)
                                    <li>{{ $attitude }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>
            <div class="bordered-bottom">
                <div class="row">
                    <strong class="col-md-6">Body Condition</strong>
                    <div class="col-md-6">
                        @if(isset($admission) && $exam)
                            {{ $exam->bcs }}
                        @else
                            <ul class="list-unstyled">
                                @foreach(ExamOptions::$bodyConditions as $bcs)
                                    <li>{{ $bcs }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
            </div>

            <div class="text-field">
                <div class="row">
                    <div class="col-md-4">
                        <strong>PCV</strong>
                    </div>
                    <div class="col-md-4">
                        <strong>BC</strong>
                    </div>
                    <div class="col-md-4">
                        <strong>TP</strong>
                    </div>
                </div>
            </div>
            <div>
                <div class="row">
                    <div class="col-md-12">
                        <strong>Fecal</strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">

<h2>Initial Treatment</h2>

<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Plan</strong>
        </div>
    </div>
</div>
<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Fluids / Supplements</strong>
        </div>
    </div>
</div>
<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Nutrition</strong>
        </div>
    </div>
</div>
<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Medications</strong>
        </div>
    </div>
</div>
<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Examined By</strong>
            {{ isset($admission) && $exam ? $exam->examiner : '' }}
        </div>
    </div>
</div>

    </div>
    <div class="col-md-6">

<h2>Disposition</h2>

<div class="text-field">
    <div class="row">
        <div class="col-md-6">
            <strong>Disposition</strong>
            {{ isset($admission) ? $admission->patient->disposition : '' }}
        </div>
        <div class="col-md-6">
            <strong>Date</strong>
            {{ isset($admission) ? format_date($admission->patient->dispositioned_at, settings('date_format')) : '' }}
        </div>
    </div>
</div>
<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Location</strong>
            {!! isset($admission) ? $admission->patient->disposition_locale : '' !!}
        </div>
    </div>
</div>
<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Release / Transfer Type</strong>
            {{ isset($admission) ? $admission->patient->release_type : '' }}
            {{ isset($admission) ? $admission->patient->transfer_type : '' }}
        </div>
    </div>
</div>
<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Reason For Disposition</strong>
            {{ isset($admission) ? $admission->patient->reason_for_disposition : '' }}
        </div>
    </div>
</div>
<div class="text-field">
    <div class="row">
        <div class="col-md-12">
            <strong>Dispositioned By</strong>
            {{ isset($admission) ? $admission->patient->dispositioned_by : '' }}
        </div>
    </div>
</div>

    </div>
</div>

@stop
