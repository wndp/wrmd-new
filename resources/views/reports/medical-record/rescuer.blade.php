<div class="border-t border-gray-500 mt-10"></div>
<h3 class="print-caption my-4">Rescuer</h3>

<div class="grid grid-cols-12 gap-2">
    <div class="print-text col-span-6">
        <strong class="font-bold mr-2">Rescuer</strong>
        {{ $admission->patient->rescuer->organization . ' ' . $admission->patient->rescuer->fullName }}
    </div>
    <div class="print-text col-span-6">
        <strong class="font-bold mr-2">Phone</strong>
        {{ $admission->patient->rescuer->phone }}
    </div>
    <div class="print-text col-span-6">
        <strong class="font-bold mr-2">Email</strong>
        {{ $admission->patient->rescuer->email }}
    </div>
    <div class="print-text col-span-6">
        <strong class="font-bold mr-2">Alt Phone</strong>
        {{ $admission->patient->rescuer->alt_phone }}
    </div>
    <div class="print-text col-span-12">
        <strong class="font-bold mr-2">Address</strong>
        {!! $admission->patient->rescuer->full_address !!}
    </div>
    <div class="print-text col-span-12">
        <strong class="font-bold mr-2">Notes About Rescuer</strong>
        {{ $admission->patient->rescuer->notes }}
    </div>
</div>
