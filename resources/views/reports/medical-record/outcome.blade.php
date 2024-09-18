<div class="border-t border-gray-500 mt-10"></div>
<h3 class="print-caption my-4">{{ __('Outcome') }}</h3>

<div class="grid grid-cols-12 gap-2">
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Disposition') }}</strong>
        {{ $admission->patient->disposition }}
    </div>
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Transfer Type') }}</strong>
        {{ $admission->patient->transfer_type }}
    </div>
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Criminal Activity') }}</strong>
        @if($admission->patient->criminal_activity) Yes @endif
    </div>
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Disposition Date') }}</strong>
        {{ format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')) }}
    </div>
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Release Type') }}</strong>
        {{ $admission->patient->release_type }}
    </div>
    <div class="print-text col-span-4">
        <strong class="font-bold mr-2">{{ __('Saved Carcass?') }}</strong>
        @if($admission->patient->carcass_saved) Yes @endif
    </div>
    <div class="print-text col-span-12">
        <strong class="font-bold mr-2">{{ __('Address') }}</strong>
        {{ $admission->patient->disposition_location }} {{ $admission->patient->disposition_subdivision }}
    </div>
</div>
