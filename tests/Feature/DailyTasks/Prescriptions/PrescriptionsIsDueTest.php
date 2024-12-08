<?php

namespace Tests\Feature\DailyTasks\Prescriptions;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\SettingKey;
use App\Models\Prescription;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class PrescriptionsIsDueTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;
    use CreatesUiBehavior;

    /**
     * @var \App\Domain\AccountPatients\AccountPatient
     */
    protected $case;

    protected function setUp(): void
    {
        parent::setUp();

        $team = Team::factory()->create();
        $this->case = $this->createCase();

        $this->setSetting($team, SettingKey::TIMEZONE, 'UTC');
    }

    #[Test]
    public function it_determines_that_a_prescription_that_starts_today_is_due_today(): void
    {
        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now(),
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertTrue($prescription->isDueToday());
    }

    #[Test]
    public function it_determines_that_a_prescription_that_has_ended_is_not_due_today(): void
    {
        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(5),
            'rx_ended_at' => Carbon::now()->subDays(1),
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertFalse($prescription->isDueToday());
    }

    #[Test]
    public function it_determines_that_a_prescription_started_in_the_past_and_ending_in_the_future_is_due_today(): void
    {
        $endlessPrescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(2),
            'rx_ended_at' => Carbon::now()->addDays(2),
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertTrue($endlessPrescription->isDueToday());
    }

    #[Test]
    public function it_determines_that_a_prescription_started_in_the_past_and_ending_today_is_due_today(): void
    {
        $endlessPrescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(2),
            'rx_ended_at' => Carbon::now(),
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertTrue($endlessPrescription->isDueToday());
    }

    #[Test]
    public function it_determines_that_a_prescription_with_a_q2d_frequency_that_started_2n_days_ago_is_due_today(): void
    {
        $frequencyIsQ2DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS
        )->attribute_option_id;

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(2),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ2DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertTrue($prescription->isDueToday());
    }

    #[Test]
    public function it_determines_that_a_prescription_with_a_q2d_frequency_that_started_not_2n_days_ago_is_not_due_today(): void
    {
        $frequencyIsQ2DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS
        )->attribute_option_id;

        $this->createUiBehavior($frequencyIsQ2DaysId, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SCATTERED_DAYS);
        $this->createUiBehavior(AttributeOptionName::DAILY_TASK_FREQUENCIES, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SCATTERED_DAYS);

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(1),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ2DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertFalse($prescription->isDueToday());
    }

    #[Test]
    public function it_determines_that_a_prescription_with_a_q3d_frequency_that_started_3n_days_ago_is_due_today(): void
    {
        $frequencyIsQ3DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_3_DAYS
        )->attribute_option_id;

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(3),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ3DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertTrue($prescription->isDueToday());

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(6),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ3DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertTrue($prescription->isDueToday());
    }

    #[Test]
    public function it_determines_that_a_prescription_with_a_q3d_frequency_that_started_not_3n_days_ago_is_not_due_today(): void
    {
        $frequencyIsQ3DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_3_DAYS
        )->attribute_option_id;

        $this->createUiBehavior($frequencyIsQ3DaysId, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SCATTERED_DAYS);
        $this->createUiBehavior(AttributeOptionName::DAILY_TASK_FREQUENCIES, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SCATTERED_DAYS);

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(1),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ3DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertFalse($prescription->isDueToday());

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(2),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ3DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertFalse($prescription->isDueToday());
    }

    #[Test]
    public function it_determines_that_a_prescription_with_a_q4d_frequency_that_started_4n_days_ago_is_due_today(): void
    {
        $frequencyIsQ4DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_4_DAYS
        )->attribute_option_id;

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(4),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ4DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertTrue($prescription->isDueToday());

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(8),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ4DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertTrue($prescription->isDueToday());
    }

    #[Test]
    public function it_determines_that_a_prescription_with_a_q4d_frequency_that_started_not_4n_days_ago_is_not_due_today(): void
    {
        $frequencyIsQ4DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_4_DAYS
        )->attribute_option_id;

        $this->createUiBehavior($frequencyIsQ4DaysId, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SCATTERED_DAYS);
        $this->createUiBehavior(AttributeOptionName::DAILY_TASK_FREQUENCIES, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SCATTERED_DAYS);

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(1),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ4DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertFalse($prescription->isDueToday());

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(2),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ4DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertFalse($prescription->isDueToday());
    }

    #[Test]
    public function it_determines_that_a_prescription_with_a_q7d_frequency_that_started_7n_days_ago_is_due_today(): void
    {
        $frequencyIsQ7DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_7_DAYS
        )->attribute_option_id;

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(7),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ7DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertTrue($prescription->isDueToday());

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(14),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ7DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertTrue($prescription->isDueToday());
    }

    #[Test]
    public function it_determines_that_a_prescription_with_a_q7d_frequency_that_started_not_7n_days_ago_is_not_due_today(): void
    {
        $frequencyIsQ7DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_7_DAYS
        )->attribute_option_id;

        $this->createUiBehavior($frequencyIsQ7DaysId, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SCATTERED_DAYS);
        $this->createUiBehavior(AttributeOptionName::DAILY_TASK_FREQUENCIES, AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SCATTERED_DAYS);

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(1),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ7DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertFalse($prescription->isDueToday());

        $prescription = Prescription::factory()->create([
            'rx_started_at' => Carbon::now()->subDays(2),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsQ7DaysId,
            'patient_id' => $this->case->patient->id,
        ]);

        $this->assertFalse($prescription->isDueToday());
    }
}
