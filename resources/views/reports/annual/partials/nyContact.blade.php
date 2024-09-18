<?php $name = collect(explode(' ', $account->contact_name, 2)); ?>

<div style="position:absolute; top:50mm; left:47mm; width: 100%; font-size: 14px;">
    {{ $name->get(0) }}
</div>
<div style="position:absolute; top:50mm; left:127mm; width: 100%; font-size: 14px;">
    {{ $name->get(1) }}
</div>

{{-- <div style="position:absolute; top:52mm; left:47mm; width: 100%; font-size: 14px;">
    {{ $account->organization }}
</div> --}}

<div style="position:absolute; top:59mm; left:47mm; width: 100%; font-size: 14px;">
    {{ $account->address }}
</div>
<div style="position:absolute; top:59mm; left:190mm; width: 100%; font-size: 14px;">
    {{ $account->city }}
</div>

<div style="position:absolute; top:67mm; left:212mm; width: 100%; font-size: 14px;">
    {{ $account->subdivision }}
</div>

<div style="position:absolute; top:67mm; left:232mm; width: 100%; font-size: 14px;">
    {{ $account->postal_code }}
</div>

<div style="position:absolute; top:76mm; left:47mm; font-size: 14px;">
    {{ $account->contact_email }}
</div>
<div style="position:absolute; top:76mm; left:220mm; width: 100%; font-size: 14px;">
    {{ $account->phone_number }}
</div>

<div style="position:absolute; top:86mm; left:47mm; width: 100%; font-size: 14px;">
    {{ $account->subdivision_permit_number }}
</div>
<div style="position:absolute; top:86mm; left:135mm; width: 100%; font-size: 14px;">
    {{ $account->federal_permit_number }}
</div>
