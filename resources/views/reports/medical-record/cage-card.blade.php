<h1 class="print-title">{{ __('Case Number') }}: {{ $admission->caseNumber }} <span style="margin-left:50px">{{ __('Species') }}: {{ $admission->patient->common_name }}</span></h1>

<div class="grid grid-cols-12 gap-2 mt-20">
    <div class="print-text col-span-6">
        <strong class="font-bold mr-2">{{ __('Date Admitted') }}</strong>
        {{ format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format').' g:i a') }}
    </div>
    <div class="print-text col-span-3">
        <strong class="font-bold mr-2">{{ __('Band') }}</strong>
        {{ $admission->patient->band }}
    </div>
    <div class="print-text col-span-3">
        <strong class="font-bold mr-2">{{ __('Name') }}</strong>
        {{ $admission->patient->name }}
    </div>
    <div class="print-text col-span-6">
        <strong class="font-bold mr-2">{{ __('Reference Number') }}</strong>
        {{ $admission->patient->reference_number }}
    </div>
    <div class="print-text col-span-6">
        <strong class="font-bold mr-2">{{ __('Microchip Number') }}</strong>
        {{ $admission->patient->microchip_number }}
    </div>
</div>
