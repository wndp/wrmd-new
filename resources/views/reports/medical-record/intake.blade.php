<div class="border-t border-gray-500 mt-10"></div>
<h3 class="print-caption my-4">{{ __('Intake') }}</h3>

<div class="grid grid-cols-12 gap-2">
    <div class="print-text col-span-12">
        <strong class="font-bold mr-2">{{ __('Admitted By') }}</strong>
        {{ $admission->patient->admitted_by }}
    </div>
    <div class="print-text col-span-8">
        <strong class="font-bold mr-2">{{ __('Address Found') }}</strong>
        {!! $admission->patient->location_found !!}
    </div>
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Date Found') }}</strong>
        {{ format_date($admission->patient->found_at, $account->settingsStore()->get('date_format')) }}
    </div>
    <div class="print-text col-span-12">
        <strong class="font-bold mr-2">{{ __('Reasons For Admission') }}</strong>
        {{ $admission->patient->reasons_for_admission }}
    </div>
    <div class="print-text col-span-12">
        <strong class="font-bold mr-2">{{ __('Care by Rescuer') }}</strong>
        {{ $admission->patient->care_by_rescuer ?? ' ' }}
    </div>
    <div class="print-text col-span-12">
        <strong class="font-bold mr-2">{{ __('Notes About Rescue') }}</strong>
        {{ $admission->patient->notes_about_rescue }}
    </div>
</div>
