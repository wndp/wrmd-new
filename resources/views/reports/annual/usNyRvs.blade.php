<?php $name = collect(explode(' ', $account->contact_name, 2)); ?>

<div style="position:absolute; top:54mm; left:30mm; width: 100%; font-size: 14px;">
    {{ $name->get(1) }}
</div>
<div style="position:absolute; top:54mm; left:100mm; width: 100%; font-size: 14px;">
    {{ $name->get(0) }}
</div>
<div style="position:absolute; top:62mm; left:30mm; width: 100%; font-size: 14px;">
    {{ $account->address }}
</div>
<div style="position:absolute; top:62mm; left:130mm; width: 100%; font-size: 14px;">
    {{ $account->city }}
</div>
<div style="position:absolute; top:70mm; left:30mm; width: 100%; font-size: 14px;">
    {{ $account->county }}
</div>
<div style="position:absolute; top:70mm; left:150mm; width: 100%; font-size: 14px;">
    {{ $account->subdivision }}
</div>
<div style="position:absolute; top:70mm; left:175mm; width: 100%; font-size: 14px;">
    {{ $account->postal_code }}
</div>
<div style="position:absolute; top:79mm; left:30mm; width: 100%; font-size: 14px;">
    {{ $account->contact_email }}
</div>
<div style="position:absolute; top:79mm; left:155mm; width: 100%; font-size: 14px;">
    {{ $account->phone_number }}
</div>
<div style="position:absolute; top:89mm; left:30mm; width: 100%; font-size: 14px;">
    {{ $account->subdivision_permit_number }}
</div>
<div style="position:absolute; top:89mm; left:100mm; width: 100%; font-size: 14px;">
    {{ $account->federal_permit_number }}
</div>

<div style="position:absolute; top:118mm; left:85mm;">
    <table style="width:125mm; background-color: transparent; font-size: 14px">
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('bats')['released'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('raccoons')['released'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('skunks')['released'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('other')['released'] }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('bats')['pending'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('raccoons')['pending'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('skunks')['pending'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('other')['pending'] }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('bats')['transferred'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('raccoons')['transferred'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('skunks')['transferred'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('other')['transferred'] }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('bats')['transferred_non_releasable'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('raccoons')['transferred_non_releasable'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('skunks')['transferred_non_releasable'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('other')['transferred_non_releasable'] }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('bats')['doa'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('raccoons')['doa'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('skunks')['doa'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('other')['doa'] }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('bats')['died'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('raccoons')['died'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('skunks')['died'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('other')['died'] }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('bats')['euthanized'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('raccoons')['euthanized'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('skunks')['euthanized'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('other')['euthanized'] }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('bats')['total'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('raccoons')['total'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('skunks')['total'] }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $speciesTotals->get('other')['total'] }}</td>
        </tr>
    </table>
</div>
