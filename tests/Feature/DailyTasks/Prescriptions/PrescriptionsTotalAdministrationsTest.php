<?php

namespace Tests\Feature\DailyTasks\Prescriptions;

use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Prescription;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class PrescriptionsTotalAdministrationsTest extends TestCase
{
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_an_open_sid_prescription_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIs1DailyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_1_DAILY
        )->attribute_option_id;

        $this->assertSame(INF, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => null,
            'frequency_id' => $frequencyIs1DailyId,
        ])->total_administrations, 'Failed asserting that start->now, no end date INF dosages.');
    }

    public function test_an_sd_prescription_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsSdId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_SINGLE_DOSE
        )->attribute_option_id;

        $this->assertSame(1, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => null,
            'frequency_id' => $frequencyIsSdId,
        ])->total_administrations, 'Failed asserting that start->now, no end date, sd is 1 dosages.');
    }

    public function test_an_sid_prescription_starting_and_ending_on_the_same_date_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsSidId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_1_DAILY
        )->attribute_option_id;

        $this->assertSame(1, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsSidId,
        ])->total_administrations, 'Failed asserting that start->now, end->now, sid is 1 dosages.');
    }

    public function test_a_sid_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsSidId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_1_DAILY
        )->attribute_option_id;

        $this->assertSame(3, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(2),
            'frequency_id' => $frequencyIsSidId,
        ])->total_administrations, 'Failed asserting that start->now, end->two days from now, sid is 3 dosages.');
    }

    public function test_a_bid_prescription_starting_and_ending_on_the_same_dates_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsBidId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_2_DAILY
        )->attribute_option_id;

        $this->assertSame(2, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now(),
            'frequency_id' => $frequencyIsBidId,
        ])->total_administrations, 'Failed asserting that start->now, end->now, bid is 2 dosages.');
    }

    public function test_a_tid_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsTidId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_3_DAILY
        )->attribute_option_id;

        $this->assertSame(6, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDay(),
            'frequency_id' => $frequencyIsTidId,
        ])->total_administrations, 'Failed asserting that start->now, end->tomorrow, tid is 6 dosages.');
    }

    public function test_a_qid_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsQidId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_4_DAILY
        )->attribute_option_id;

        $this->assertSame(8, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDay(),
            'frequency_id' => $frequencyIsQidId,
        ])->total_administrations, 'Failed asserting that start->now, end->tomorrow, qid is 8 dosages.');
    }

    public function test_a_q2d_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsQ2DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS
        )->attribute_option_id;

        $this->assertSame(3, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(4),
            'frequency_id' => $frequencyIsQ2DaysId,
        ])->total_administrations, 'Failed asserting that start->now, end->4 days from now, q2d is 3 dosages.');
    }

    public function test_a_q3d_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsQ3DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_3_DAYS
        )->attribute_option_id;

        $this->assertSame(4, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(11),
            'frequency_id' => $frequencyIsQ3DaysId,
        ])->total_administrations, 'Failed asserting that start->now, end->11 days from now, q3d is 4 dosages.');
    }

    public function test_a_q4d_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsQ4DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_4_DAYS
        )->attribute_option_id;

        $this->assertSame(2, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(7),
            'frequency_id' => $frequencyIsQ4DaysId,
        ])->total_administrations, 'Failed asserting that start->now, end->7 days from now, q4d is 2 dosages.');
    }

    public function test_a_q5d_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsQ5DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_5_DAYS
        )->attribute_option_id;

        $this->assertSame(2, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(7),
            'frequency_id' => $frequencyIsQ5DaysId,
        ])->total_administrations, 'Failed asserting that start->now, end->7 days from now, q4d is 2 dosages.');
    }

    public function test_a_q7d_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsQ7DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_7_DAYS
        )->attribute_option_id;

        $this->assertSame(3, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(14),
            'frequency_id' => $frequencyIsQ7DaysId,
        ])->total_administrations, 'Failed asserting that start->now, end->14 days from now, q7d is 2 dosages.');
    }

    public function test_a_q14d_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsQ14DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_14_DAYS
        )->attribute_option_id;

        $this->assertSame(2, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(14),
            'frequency_id' => $frequencyIsQ14DaysId,
        ])->total_administrations, 'Failed asserting that start->now, end->14 days from now, q14d is 2 dosages.');
    }

    public function test_a_q21d_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsQ21DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_21_DAYS
        )->attribute_option_id;

        $this->assertSame(2, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(21),
            'frequency_id' => $frequencyIsQ21DaysId,
        ])->total_administrations, 'Failed asserting that start->now, end->21 days from now, q21d is 2 dosages.');
    }

    public function test_a_q28d_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $frequencyIsQ28DaysId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_28_DAYS
        )->attribute_option_id;

        $this->assertSame(2, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(28),
            'frequency_id' => $frequencyIsQ28DaysId,
        ])->total_administrations, 'Failed asserting that start->now, end->28 days from now, q28d is 2 dosages.');
    }

    public function test_an_om_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $this->assertSame(4, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(3),
            //'frequency_id' => 'om',
        ])->total_administrations, 'Failed asserting that start->now, end->3 days from now, om is 4 dosages.');
    }

    public function test_an_on_prescription_starting_and_ending_on_different_dates_knows_how_many_total_administrations_it_is(): void
    {
        $this->assertSame(4, Prescription::factory()->make([
            'rx_started_at' => Carbon::now(),
            'rx_ended_at' => Carbon::now()->addDays(3),
            //'frequency_id' => 'on',
        ])->total_administrations, 'Failed asserting that start->now, end->3 days from now, on is 4 dosages.');
    }
}
