<?php

namespace Tests\Feature\DailyTasks\Prescriptions;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Collection;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class PrescriptionAdministrationsTest extends TestCase
{
    use CreatesUiBehavior;
    use RefreshDatabase;

    #[Test]
    public function aPrescriptionHasAnAppendedAdministrationsAttribute(): void
    {
        $this->assertInstanceOf(Collection::class, Prescription::factory()->make()->administrations);
    }

    #[Test]
    public function aPrescriptionAdministrationShouldAppendAnAdministeredAtDate(): void
    {
        $now = Carbon::now();

        $prescription = Prescription::factory()->make([
            'rx_started_at' => $now,
        ]);

        $this->assertEquals($now->timestamp, $prescription->administrations->first()->administered_at->timestamp);
    }

    #[Test]
    public function theAdministrationsCollectionShouldOnlyHaveAnInfinatePrescriptionOnce(): void
    {
        $frequencyIs1DailyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_1_DAILY
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => null,
            'frequency_id' => $frequencyIs1DailyId,
        ]);

        $this->assertCount(1, $prescription->administrations);
        $this->assertTrue($prescription->administrations->first()->is($prescription));
    }

    #[Test]
    public function aSingleDosePrescriptionShouldOnlyBeTheAdministrationsCollectionOnce(): void
    {
        $frequencyIsSdId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SINGLE_DOSE
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => null,
            'frequency_id' => $frequencyIsSdId,
        ]);

        $this->assertCount(1, $prescription->administrations);
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(0)->administered_at->toDateString());
    }

    #[Test]
    public function anSidPrescriptionAdministrationsAdministeredAtDateShouldBeIncremented(): void
    {
        $frequencyIs1DailyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_1_DAILY
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(2),
            'frequency_id' => $frequencyIs1DailyId,
        ]);

        $this->assertCount(3, $prescription->administrations);
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(0)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(1)->toDateString(), $prescription->administrations->get(1)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(2)->toDateString(), $prescription->administrations->get(2)->administered_at->toDateString());
    }

    #[Test]
    public function aBidPrescriptionAdministrationsAdministeredAtDateShouldBeIncremented(): void
    {
        $frequencyIs2DailyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_2_DAILY
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(2),
            'frequency_id' => $frequencyIs2DailyId,
        ]);

        $this->assertCount(6, $prescription->administrations);
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(0)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(1)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(1)->toDateString(), $prescription->administrations->get(2)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(1)->toDateString(), $prescription->administrations->get(3)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(2)->toDateString(), $prescription->administrations->get(4)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(2)->toDateString(), $prescription->administrations->get(5)->administered_at->toDateString());
    }

    #[Test]
    public function aTidPrescriptionAdministrationsAdministeredAtDateShouldBeIncremented(): void
    {
        $frequencyIs3DailyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_3_DAILY
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(1),
            'frequency_id' => $frequencyIs3DailyId,
        ]);

        $this->assertCount(6, $prescription->administrations);
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(0)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(1)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(2)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(1)->toDateString(), $prescription->administrations->get(3)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(1)->toDateString(), $prescription->administrations->get(4)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(1)->toDateString(), $prescription->administrations->get(5)->administered_at->toDateString());
    }

    #[Test]
    public function aQidPrescriptionAdministrationsAdministeredAtDateShouldBeIncremented(): void
    {
        $frequencyIs4DailyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_4_DAILY
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(1),
            'frequency_id' => $frequencyIs4DailyId,
        ]);

        $this->assertCount(8, $prescription->administrations);
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(0)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(1)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(2)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(3)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(1)->toDateString(), $prescription->administrations->get(4)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(1)->toDateString(), $prescription->administrations->get(5)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(1)->toDateString(), $prescription->administrations->get(6)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(1)->toDateString(), $prescription->administrations->get(7)->administered_at->toDateString());
    }

    #[Test]
    public function aQ2dPrescriptionAdministrationsAdministeredAtDateShouldBeIncremented(): void
    {
        $frequencyIsQ2DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(4),
            'frequency_id' => $frequencyIsQ2DaysId,
        ]);

        $this->assertCount(3, $prescription->administrations);
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(0)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(2)->toDateString(), $prescription->administrations->get(1)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(4)->toDateString(), $prescription->administrations->get(2)->administered_at->toDateString());
    }

    #[Test]
    public function aQ3dPrescriptionAdministrationsAdministeredAtDateShouldBeIncremented(): void
    {
        $frequencyIsQ3DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_3_DAYS
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(6),
            'frequency_id' => $frequencyIsQ3DaysId,
        ]);

        $this->assertCount(3, $prescription->administrations);
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(0)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(3)->toDateString(), $prescription->administrations->get(1)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(6)->toDateString(), $prescription->administrations->get(2)->administered_at->toDateString());
    }

    #[Test]
    public function aQ4dPrescriptionAdministrationsAdministeredAtDateShouldBeIncremented(): void
    {
        $frequencyIsQ4DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_4_DAYS
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(8),
            'frequency_id' => $frequencyIsQ4DaysId,
        ]);

        $this->assertCount(3, $prescription->administrations);
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(0)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(4)->toDateString(), $prescription->administrations->get(1)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(8)->toDateString(), $prescription->administrations->get(2)->administered_at->toDateString());
    }

    #[Test]
    public function aQ7dPrescriptionAdministrationsAdministeredAtDateShouldBeIncremented(): void
    {
        $frequencyIsQ7DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_7_DAYS
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(14),
            'frequency_id' => $frequencyIsQ7DaysId,
        ]);

        $this->assertCount(3, $prescription->administrations);
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(0)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(7)->toDateString(), $prescription->administrations->get(1)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(14)->toDateString(), $prescription->administrations->get(2)->administered_at->toDateString());
    }

    #[Test]
    public function aQ14dPrescriptionAdministrationsAdministeredAtDateShouldBeIncremented(): void
    {
        $frequencyIsQ14DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_14_DAYS
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(28),
            'frequency_id' => $frequencyIsQ14DaysId,
        ]);

        $this->assertCount(3, $prescription->administrations);
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(0)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(14)->toDateString(), $prescription->administrations->get(1)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(28)->toDateString(), $prescription->administrations->get(2)->administered_at->toDateString());
    }

    #[Test]
    public function aQ21dPrescriptionAdministrationsAdministeredAtDateShouldBeIncremented(): void
    {
        $frequencyIsQ21DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_21_DAYS
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(42),
            'frequency_id' => $frequencyIsQ21DaysId,
        ]);

        $this->assertCount(3, $prescription->administrations);
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(0)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(21)->toDateString(), $prescription->administrations->get(1)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(42)->toDateString(), $prescription->administrations->get(2)->administered_at->toDateString());
    }

    #[Test]
    public function aQ28dPrescriptionAdministrationsAdministeredAtDateShouldBeIncremented(): void
    {
        $frequencyIsQ28DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_28_DAYS
        )->attribute_option_id;

        $prescription = Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(56),
            'frequency_id' => $frequencyIsQ28DaysId,
        ]);

        $this->assertCount(3, $prescription->administrations);
        $this->assertEquals(Carbon::now()->toDateString(), $prescription->administrations->get(0)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(28)->toDateString(), $prescription->administrations->get(1)->administered_at->toDateString());
        $this->assertEquals(Carbon::now()->addDays(56)->toDateString(), $prescription->administrations->get(2)->administered_at->toDateString());
    }
}
