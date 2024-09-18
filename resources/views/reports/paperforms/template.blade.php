<div style="position:absolute; top:16mm; left:15mm; width:{{ $availableWidth * 0.25 }}mm;">
    <strong class="bold">Case#</strong>
    {{ isset($admission) ? $admission->caseNumber : '' }}
</div>
<div style="position:absolute; top:16mm; left:{{ 15 + $availableWidth * 0.25 }}mm; width:{{ $availableWidth * 0.416 }}mm;">
    <strong class="bold">Species</strong>
    {{ isset($admission) ? $admission->patient->common_name : '' }}
</div>
<div style="position:absolute; top:16mm; left:{{ 15 + $availableWidth * 0.25 + $availableWidth * 0.416 }}mm; width:{{ $availableWidth * 0.333 }}mm;">
    <strong class="bold">Date Admitted</strong>
    {{ isset($admission) ? format_date($admission->patient->admitted_at, settings('date_format')) : '' }}
</div>
<div class="text-field" style="position:absolute; top:20mm; left:15mm; width:{{ $availableWidth }}mm;"></div>


<div style="position:absolute; top:22mm; left:15mm; width:{{ $availableWidth * 0.25 }}mm;">
    <strong class="bold">Band</strong>
    {{ isset($admission) ? $admission->patient->band : '' }}
</div>
<div style="position:absolute; top:22mm; left:{{ 15 + $availableWidth * 0.25 }}mm; width:{{ $availableWidth * 0.416 }}mm;">
    <strong class="bold">Reference #</strong>
    {{ isset($admission) ? $admission->patient->reference_number : '' }}
</div>
<div style="position:absolute; top:22mm; left:{{ 15 + $availableWidth * 0.25 + $availableWidth * 0.416 }}mm; width:{{ $availableWidth * 0.333 }}mm;">
    <strong class="bold">Name</strong>
    {{ isset($admission) ? $admission->patient->name : '' }}
</div>
<div class="text-field" style="position:absolute; top:26mm; left:15mm; width:{{ $availableWidth }}mm;"></div>


<div style="position:absolute; top:28mm; left:15mm; width:{{ $availableWidth * 0.5 }}mm;">
    <strong class="bold">Date Found</strong>
    {{ isset($admission) ? format_date($admission->patient->found_at, settings('date_format')) : '' }}
</div>
<div style="position:absolute; top:28mm; left:{{ 15 + $availableWidth * 0.5 }}mm; width:{{ $availableWidth * 0.5 }}mm;">
    <strong class="bold">Criminal Activity?</strong>
    @if(isset($admission) and $admission->patient->is_criminal_activity) Yes @else Yes / No @endif
</div>
<div class="text-field" style="position:absolute; top:32mm; left:15mm; width:{{ $availableWidth }}mm;"></div>


<div style="position:absolute; top:34mm; left:15mm; width:{{ $availableWidth * 0.85 }}mm;">
    <strong class="bold">Location Found</strong>
    {!! isset($admission) ? $admission->patient->location_found : '' !!}
</div>
<div class="text-field" style="position:absolute; top:38mm; left:15mm; width:{{ $availableWidth }}mm;"></div>


<div style="position:absolute; top:40mm; left:15mm; width:{{ $availableWidth * 0.85 }}mm;">
    <strong class="bold">Reasons for Admission</strong>
    {{ isset($admission) ? $admission->patient->reasons_for_admission : '' }}
</div>
<div class="text-field" style="position:absolute; top:44mm; left:15mm; width:{{ $availableWidth }}mm;"></div>


<div style="position:absolute; top:46mm; left:15mm; width:{{ $availableWidth * 0.85 }}mm;">
    <strong class="bold">Care by Rescuer</strong>
    {{ isset($admission) ? $admission->patient->care_by_rescuer : '' }}
</div>
<div class="text-field" style="position:absolute; top:50mm; left:15mm; width:{{ $availableWidth }}mm;"></div>


<div style="position:absolute; top:52mm; left:15mm; width:{{ $availableWidth * 0.5 }}mm;">
    <strong class="bold">Rescuers Name</strong>
    {{ isset($admission) ? $admission->patient->rescuer->fullName : '' }}
</div>
<div style="position:absolute; top:52mm; left:{{ 15 + $availableWidth * 0.5 }}mm; width:{{ $availableWidth * 0.5 }}mm;">
    <strong class="bold">Rescuers Phone</strong>
    {{ isset($admission) ? $admission->patient->rescuer->phone : '' }}
</div>
<div class="text-field" style="position:absolute; top:56mm; left:15mm; width:{{ $availableWidth }}mm;"></div>


<div style="position:absolute; top:58mm; left:15mm; width:{{ $availableWidth * 0.85 }}mm;">
    <strong class="bold">Rescuers Address</strong>
    {!! isset($admission) ? $admission->patient->rescuer->full_address : '' !!}
</div>
<div class="text-field" style="position:absolute; top:62mm; left:15mm; width:{{ $availableWidth }}mm;"></div>


<div style="position:absolute; top:64mm; left:15mm; width:{{ $availableWidth * 0.85 }}mm;">
    <strong class="bold">Notes About Rescuer</strong>
    {{ isset($admission) ? $admission->patient->rescuer->notes : '' }}
</div>
<div class="text-field" style="position:absolute; top:68mm; left:15mm; width:{{ $availableWidth }}mm;"></div>
