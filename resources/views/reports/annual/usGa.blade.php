@extends('report')

@section('body')

<h1 class="print-title">{{ $year }} {{ $title }}</h1>

@include('reports.annual.partials.header')

<table class="print-table">
    <caption>Species Groups</caption>
    <thead>
        <tr>
            <td style="width:32mm">Species Groups</td>
            <td style="width:21mm">Released to the Wild</td>
            <td style="width:21mm">Still in Care</td>
            <td style="width:21mm">Transferred</td>
            <td style="width:21mm">Dead on Arrival</td>
            <td style="width:21mm">Died Under Care</td>
            <td style="width:21mm">Euthanized</td>
            <td style="width:21mm">Holding Indefinitely</td>
        </tr>
        <tr>
            <td>Raccoon, Skunk, Fox, Bobcat</td>
            <td>{{ $a1 = $rabiesVectors->sum('released') }}</td>
            <td>{{ $b1 = $rabiesVectors->sum('pending') }}</td>
            <td>{{ $c1 = $rabiesVectors->sum('transferred') }}</td>
            <td>{{ $d1 = $rabiesVectors->sum('doa') }}</td>
            <td>{{ $e1 = $rabiesVectors->sum('died') }}</td>
            <td>{{ $f1 = $rabiesVectors->sum('euthanized') }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Other Small Mammals</td>
            <td>{{ $a2 = $nonRabiesVectors->sum('released') }}</td>
            <td>{{ $b2 = $nonRabiesVectors->sum('pending') }}</td>
            <td>{{ $c2 = $nonRabiesVectors->sum('transferred') }}</td>
            <td>{{ $d2 = $nonRabiesVectors->sum('doa') }}</td>
            <td>{{ $e2 = $nonRabiesVectors->sum('died') }}</td>
            <td>{{ $f2 = $nonRabiesVectors->sum('euthanized') }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Bats</td>
            <td>{{ $a3 = $bats->sum('released') }}</td>
            <td>{{ $b3 = $bats->sum('pending') }}</td>
            <td>{{ $c3 = $bats->sum('transferred') }}</td>
            <td>{{ $d3 = $bats->sum('doa') }}</td>
            <td>{{ $e3 = $bats->sum('died') }}</td>
            <td>{{ $f3 = $bats->sum('euthanized') }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Deer</td>
            <td>{{ $a4 = $deer->sum('released') }}</td>
            <td>{{ $b4 = $deer->sum('pending') }}</td>
            <td>{{ $c4 = $deer->sum('transferred') }}</td>
            <td>{{ $d4 = $deer->sum('doa') }}</td>
            <td>{{ $e4 = $deer->sum('died') }}</td>
            <td>{{ $f4 = $deer->sum('euthanized') }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Birds, Except Birds of Prey</td>
            <td>{{ $a5 = $nonRaptors->sum('released') }}</td>
            <td>{{ $b5 = $nonRaptors->sum('pending') }}</td>
            <td>{{ $c5 = $nonRaptors->sum('transferred') }}</td>
            <td>{{ $d5 = $nonRaptors->sum('doa') }}</td>
            <td>{{ $e5 = $nonRaptors->sum('died') }}</td>
            <td>{{ $f5 = $nonRaptors->sum('euthanized') }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Birds of Prey</td>
            <td>{{ $a6 = $raptors->sum('released') }}</td>
            <td>{{ $b6 = $raptors->sum('pending') }}</td>
            <td>{{ $c6 = $raptors->sum('transferred') }}</td>
            <td>{{ $d6 = $raptors->sum('doa') }}</td>
            <td>{{ $e6 = $raptors->sum('died') }}</td>
            <td>{{ $f6 = $raptors->sum('euthanized') }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Reptiles &amp; Amphibians</td>
            <td>{{ $a7 = $herpitiles->sum('released') }}</td>
            <td>{{ $b7 = $herpitiles->sum('pending') }}</td>
            <td>{{ $c7 = $herpitiles->sum('transferred') }}</td>
            <td>{{ $d7 = $herpitiles->sum('doa') }}</td>
            <td>{{ $e7 = $herpitiles->sum('died') }}</td>
            <td>{{ $f7 = $herpitiles->sum('euthanized') }}</td>
            <td></td>
        </tr>
        <tr>
            <td>Totals</td>
            <td>{{ array_sum([$a1, $a2, $a3, $a4, $a5, $a6, $a7]) }}</td>
            <td>{{ array_sum([$b1, $b2, $b3, $b4, $b5, $b6, $b7]) }}</td>
            <td>{{ array_sum([$c1, $c2, $c3, $c4, $c5, $c6, $c7]) }}</td>
            <td>{{ array_sum([$d1, $d2, $d3, $d4, $d5, $d6, $d7]) }}</td>
            <td>{{ array_sum([$e1, $e2, $e3, $e4, $e5, $e6, $e7]) }}</td>
            <td>{{ array_sum([$f1, $f2, $f3, $f4, $f5, $f6, $f7]) }}</td>
            <td></td>
        </tr>
    </thead>

</table>

<table class="print-table">
    <caption>Patients Admitted</caption>
    <thead>
        <tr>
            <th style="width:130px;">Species</th>
            <th style="width:150px;">Date Obtained</th>
            <th style="width:50mm;">Source of Animal</th>
            <th style="width:200px;">Condition Requiring Rehab</th>
            <th style="width:auto;">Treatment</th>
            <th style="width:150px;">Outcome / Date</th>
        </tr>
    </thead>
    <tbody>
        @foreach($admissions as $admission)
            <?php
            $rescuer = $admission->patient->rescuer;
            $originSource  = empty($rescuer->organization) ? $rescuer->first_name.' '.$rescuer->last_name : $rescuer->organization;
            $originSource .= '<br>' . $rescuer->county;

            $treatment = Str::contains($admission->patient->disposition, 'Euthanized')
                ? 'Euthanized'
                : $admission->patient->intake_exam->treatment;
            ?>
            <tr>
                <td><?php echo $admission->patient->common_name; ?></td>
                <td><?php echo format_date($admission->patient->admitted_at, $account->settingsStore()->get('date_format')); ?></td>
                <td><?php echo $originSource; ?></td>
                <td><?php echo $admission->patient->reasons_for_admission; ?></td>
                <td><?php echo $treatment; ?></td>
                <td><?php echo format_disposition($admission->patient->disposition) .'<br>'. format_date($admission->patient->dispositioned_at, $account->settingsStore()->get('date_format')); ?></td>
            </tr>
        @endforeach
    </tbody>
</table>

@stop
