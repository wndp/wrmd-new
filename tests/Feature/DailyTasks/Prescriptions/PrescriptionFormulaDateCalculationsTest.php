<?php

namespace Tests\Feature\DailyTasks\Prescriptions;

use App\Models\Formula;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

#[Group('daily-tasks')]
final class PrescriptionFormulaDateCalculationsTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function itStartsThePrescriptionTheDayItIsWritten(): void
    {
        $patient = Patient::factory()->create();

        $formula = Formula::factory()->create(['defaults' => [
            'start_on_prescription_date' => true,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertTrue(Carbon::parse($results['rx_started_at'])->isSameDay(Carbon::now()));
    }

    #[Test]
    public function itEndsThePrescriptionTheGiveNumberOfDaysPastTheStartDate(): void
    {
        $patient = Patient::factory()->create();

        $formula = Formula::factory()->create(['defaults' => [
            'start_on_prescription_date' => true,
            'duration' => 7,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertTrue(Carbon::parse($results['rx_ended_at'])->isSameDay(Carbon::now()->addDays(6)));
    }

    #[Test]
    public function itLeavesThePrescriptionOpenIfNoDurationIsGiven(): void
    {
        $patient = Patient::factory()->create();

        $formula = Formula::factory()->create(['defaults' => [
            'start_on_prescription_date' => true,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertNull($results['rx_ended_at']);
    }
}
