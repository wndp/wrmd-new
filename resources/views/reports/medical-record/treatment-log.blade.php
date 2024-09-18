<div class="border-t border-gray-500 mt-10"></div>
<h3 class="print-caption my-4">Treatment Log</h3>
<?php
$treatmentLogs = $admission->patient->getTreatmentLogs(
    \App\Domain\Users\User::wrmdbot(),
    ($account->settingsStore()->get('logOrder') === 'desc')
);
?>
<div class="grid grid-cols-12 gap-2">
    <strong class="print-text col-span-2 font-bold">Date</strong>
    <strong class="print-text col-span-2 font-bold">Type</strong>
    <strong class="print-text col-span-8 font-bold">Comments</strong>

    @foreach($treatmentLogs as $treatment)
        <div class="print-text col-span-2 whitespace-nowrap">
            {{ $treatment->logged_at_date_time->toDayDateTimeString() }}
        </div>
        <div class="print-text col-span-2 whitespace-nowrap">
            {{ $treatment->type }}
        </div>
        <div class="print-text col-span-8">
            {!! $treatment->body !!}
        </div>
    @endforeach
</div>
