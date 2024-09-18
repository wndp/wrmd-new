<div class="border-t border-gray-500 mt-10"></div>
<h3 class="print-caption my-4">{{ __('Intake Exam') }}</h3>

<?php $exam = $admission->patient->intake_exam; ?>

<div class="grid grid-cols-12 gap-2">
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Dehydration') }}</strong>
        {{ empty($exam) ? '' : $exam->dehydration }}
    </div>
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Weight') }}</strong>
        {{ empty($exam) ? '' : $exam->fullWeight }}
    </div>
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Sex') }}</strong>
        {{ empty($exam) ? '' : $exam->sex }}
    </div>
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Age') }}</strong>
        {{ empty($exam) ? '' : $exam->fullAge }}
    </div>
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Attitude') }}</strong>
        {{ empty($exam) ? '' : $exam->attitude }}
    </div>
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Body Condition') }}</strong>
        {{ empty($exam) ? '' : $exam->bcs }}
    </div>
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Mucous Membrane Color') }}</strong>
        {{ empty($exam) ? '' : $exam->mm_color }}
    </div>
    <div class="print-text col-span-8">
        <strong class="font-bold mr-2">{{ __('Temperature') }}</strong>
        {{ empty($exam) ? '' : $exam->fullTemperature }}
    </div>
</div>

<div class="grid grid-cols-12 gap-2 mt-4 print-text">
    <strong class="font-bold col-span-3">Body Area</strong>
    <strong class="font-bold col-span-9">Comments</strong>
    @foreach(\App\Domain\Patients\ExamOptions::$bodyParts as $bodyPart)
        <div class="print-text col-span-3" style="white-space: nowrap;">
            {{ $bodyPart }}
        </div>
        <div class="print-text col-span-9">
            {{ $exam->{$bodyPart.'_finding'} }}
            {{ empty($exam->{$bodyPart}) ? '' : ': ' . $exam->{$bodyPart} }}
        </div>
    @endforeach
    <div class="print-text col-span-12 mt-2">
        <strong class="font-bold mr-2">{{ __('Comments') }}</strong>
        {{ empty($exam) ? '' : $exam->comments }}
    </div>
    <div class="print-text col-span-12">
        <strong class="font-bold mr-2">{{ __('Treatments') }}</strong>
        {{ empty($exam) ? '' : $exam->treatment }}
    </div>
    <div class="print-text col-span-12">
        <strong class="font-bold mr-2">{{ __('Examiner') }}</strong>
        {{ empty($exam) ? '' : $exam->examiner }}
    </div>
</div>
