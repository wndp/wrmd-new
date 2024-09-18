<?php
$aves = $distressCodesTotals->where('class', 'Aves')->first();
$mammalia = $distressCodesTotals->where('class', 'Mammalia')->first();
$reptilia = $distressCodesTotals->where('class', 'Reptilia')->first();
$amphibia = $distressCodesTotals->where('class', 'Amphibia')->first();
?>

<div style="position: absolute; top: 36.5mm; left: 54.5mm;">
    <table style="width:78mm; background-color: transparent; font-size: 14px;">
        <?php
        foreach (\App\Domain\Classifications\UsNyCauseOfDistress::terms() as $code) {
            $code = Str::lower(Str::before($code, '.'));
            if (in_array($code[0], ['a','b','c','d','e','f'])) {
        ?>
        <tr>
            <td style="text-align: center; vertical-align: middle; padding: 0.50mm 0; width: 25%">@if(! is_null($aves)) {{ $aves->$code }} @else 0 @endif</td>
            <td style="text-align: center; vertical-align: middle; padding: 0.50mm 0; width: 25%">@if(! is_null($mammalia)) {{ $mammalia->$code }} @else 0 @endif</td>
            <td style="text-align: center; vertical-align: middle; padding: 0.50mm 0; width: 25%">@if(! is_null($reptilia)) {{ $reptilia->$code }} @else 0 @endif</td>
            <td style="text-align: center; vertical-align: middle; padding: 0.50mm 0; width: 25%">@if(! is_null($amphibia)) {{ $amphibia->$code }} @else 0 @endif</td>
        </tr>
        <?php
            }
        }
        ?>
    </table>
</div>

<div style="position:absolute; top: 36.5mm; left: 200mm;">
    <table style="width:78mm; background-color: transparent; font-size: 14px;">
        <?php
        foreach (\App\Domain\Classifications\UsNyCauseOfDistress::terms() as $code) {
            $code = Str::lower(Str::before($code, '.'));
            if (in_array($code[0], ['g','h','i','j','k','l','m','n','o','p'])) {
        ?>
        <tr>
            <td style="text-align: center; vertical-align: middle; padding: 0.49mm 0; width: 25%">@if(! is_null($aves)) {{ $aves->$code }} @else 0 @endif</td>
            <td style="text-align: center; vertical-align: middle; padding: 0.49mm 0; width: 25%">@if(! is_null($mammalia)) {{ $mammalia->$code }} @else 0 @endif</td>
            <td style="text-align: center; vertical-align: middle; padding: 0.49mm 0; width: 25%">@if(! is_null($reptilia)) {{ $reptilia->$code }} @else 0 @endif</td>
            <td style="text-align: center; vertical-align: middle; padding: 0.49mm 0; width: 25%">@if(! is_null($amphibia)) {{ $amphibia->$code }} @else 0 @endif</td>
        </tr>
        <?php
            }
        }
        ?>
    </table>
</div>
