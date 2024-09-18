@include('reports.annual.partials.nyContact')

<?php
$totalBirds = $birdAcquisitions->sum(function ($admission) {
    return $admission->released + $admission->pending + $admission->care + $admission->individual + $admission->institute + $admission->died + $admission->doa + $admission->euthanized;
});

$totalMammals = $mammalAcquisitions->sum(function ($admission) {
    return $admission->released + $admission->pending + $admission->care + $admission->individual + $admission->institute + $admission->died + $admission->doa + $admission->euthanized;
});

$totalReptiles = $reptileAcquisitions->sum(function ($admission) {
    return $admission->released + $admission->pending + $admission->care + $admission->individual + $admission->institute + $admission->died + $admission->doa + $admission->euthanized;
});

$totalAmphibians = $amphibianAcquisitions->sum(function ($admission) {
    return $admission->released + $admission->pending + $admission->care + $admission->individual + $admission->institute + $admission->died + $admission->doa + $admission->euthanized;
});
?>

<div style="position:absolute; top:151.5mm; left:106mm;">
    <table style="width:170mm; background-color: transparent; font-size: 14px">
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $birdAcquisitions->sum('released') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $mammalAcquisitions->sum('released') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $reptileAcquisitions->sum('released') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $amphibianAcquisitions->sum('released') }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $birdAcquisitions->sum('pending') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $mammalAcquisitions->sum('pending') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $reptileAcquisitions->sum('pending') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $amphibianAcquisitions->sum('pending') }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $birdAcquisitions->sum('care') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $mammalAcquisitions->sum('care') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $reptileAcquisitions->sum('care') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $amphibianAcquisitions->sum('care') }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $birdAcquisitions->sum('individual') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $mammalAcquisitions->sum('individual') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $reptileAcquisitions->sum('individual') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $amphibianAcquisitions->sum('individual') }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $birdAcquisitions->sum('institute') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $mammalAcquisitions->sum('institute') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $reptileAcquisitions->sum('institute') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $amphibianAcquisitions->sum('institute') }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $birdAcquisitions->sum('died') + $birdAcquisitions->sum('doa') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $mammalAcquisitions->sum('died') + $mammalAcquisitions->sum('doa') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $reptileAcquisitions->sum('died') + $reptileAcquisitions->sum('doa') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $amphibianAcquisitions->sum('died') + $amphibianAcquisitions->sum('doa') }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $birdAcquisitions->sum('euthanized') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $mammalAcquisitions->sum('euthanized') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $reptileAcquisitions->sum('euthanized') }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $amphibianAcquisitions->sum('euthanized') }}</td>
        </tr>
        <tr>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $totalBirds }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $totalMammals }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $totalReptiles }}</td>
            <td style="text-align:center; vertical-align:middle; padding:0.40mm 0; width:25%">{{ $totalAmphibians }}</td>
        </tr>
    </table>
</div>
