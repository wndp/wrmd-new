<?php

namespace Tests\Unit\Models;

use App\Models\Formula;
use App\Models\Team;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;

#[Group('daily-tasks')]
final class FormulaTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    #[Test]
    public function aFormulaBelongsToAnTeam(): void
    {
        $formula = Formula::factory()->make();

        $this->assertInstanceOf(Team::class, $formula->team);
    }

    #[Test]
    public function aFormulaIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Formula::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function aFormulasDefaultsCanBeFormattedIntoAHumanReadableSentance(): void
    {
        $formula = Formula::factory()->make([
            'defaults' => [
                'drug' => 'Metacam',
                'concentration' => 1.5,
                'concentration_unit' => 'mg/ml',
                'dosage' => 1,
                'dosage_unit' => 'mg/kg',
                'frequency' => 'BID',
                'route' => 'PO',
                'auto_calculate_dose' => true,
                'start_on_prescription_date' => true,
                'duration' => 7,
                'loading_dose' => '',
                'loading_dose_unit' => '',
                'is_controlled_substance' => false,
                'instructions' => 'Special instruction.',
            ],
        ]);

        $this->assertEquals('Auto-calculated dose of 1.5mg/ml Metacam 1mg/kg PO BID for 7 days. Special instruction.', $formula->defaults_for_humans);

        $formula = Formula::factory()->make([
            'defaults' => [
                'drug' => 'Buprenorphine',
                'dosage' => 0.1,
                'dosage_unit' => 'mg/kg',
                'dose' => 0.01,
                'dose_unit' => 'ml',
                'frequency' => 'SD',
                'route' => 'IM',
                'auto_calculate_dose' => false,
                'start_on_prescription_date' => true,
                'is_controlled_substance' => true,
            ],
        ]);

        $this->assertEquals('0.01ml of Buprenorphine 0.1mg/kg IM SD.', $formula->defaults_for_humans);
    }

    #[Test]
    public function formulsCanBeSearchedFor(): void
    {
        $formula = Formula::factory()->create([
            'name' => 'FOO formula',
            'defaults' => [
                'drug' => 'BAR drug',
            ],
        ]);

        $this->assertTrue(Formula::query()->search('foo')->first()->is($formula));
        $this->assertTrue(Formula::query()->search('bar')->first()->is($formula));
    }
}
