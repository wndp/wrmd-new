<?php

namespace Tests\Feature\DailyTasks\Prescriptions;

use App\Models\Formula;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\CreatesTeamUser;

#[Group('daily-tasks')]
final class PrescriptionFormulaDateCalculationsTest extends TestCase
{
    use CreatesTeamUser;
    use RefreshDatabase;

    public function test_it_starts_the_prescription_the_day_it_is_written(): void
    {
        $patient = Patient::factory()->create();

        $formula = Formula::factory()->create(['defaults' => [
            'start_on_prescription_date' => true,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertTrue(Carbon::parse($results['rx_started_at'])->isSameDay(Carbon::now()));
    }

    public function test_it_ends_the_prescription_the_give_number_of_days_past_the_start_date(): void
    {
        $patient = Patient::factory()->create();

        $formula = Formula::factory()->create(['defaults' => [
            'start_on_prescription_date' => true,
            'duration' => 7,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertTrue(Carbon::parse($results['rx_ended_at'])->isSameDay(Carbon::now()->addDays(6)));
    }

    public function test_it_leaves_the_prescription_open_if_no_duration_is_given(): void
    {
        $patient = Patient::factory()->create();

        $formula = Formula::factory()->create(['defaults' => [
            'start_on_prescription_date' => true,
        ]]);

        $results = $formula->calculatedAttributes($patient);

        $this->assertNull($results['rx_ended_at']);
    }
}
