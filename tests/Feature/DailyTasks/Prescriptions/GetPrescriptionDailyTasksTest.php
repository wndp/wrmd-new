<?php

namespace Tests\Feature\DailyTasks\Prescriptions;

use App\Actions\GetPrescriptionDailyTasks;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\SettingKey;
use App\Models\Prescription;
use App\Support\DailyTasksFilters;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class GetPrescriptionDailyTasksTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_nothing_is_returned_if_prescription_are_not_included(): void
    {
        $result = GetPrescriptionDailyTasks::handle(
            new DailyTasksFilters(['include' => '']),
            []
        );

        $this->assertNull($result);
    }

    public function test_it_gets_a_patients_prescription(): void
    {
        $frequencyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS
        );

        $me = $this->createTeamUser();

        $this->registerSettingsContainer($me->team);

        $admission = $this->createCase($me->team);

        $recheck = Prescription::factory()->create([
            'patient_id' => $admission->patient_id,
            'rx_started_at' => Carbon::now(Wrmd::settings(SettingKey::TIMEZONE)),
            'rx_ended_at' => null,
            'frequency_id' => $frequencyId,
        ]);

        $result = GetPrescriptionDailyTasks::handle(
            new DailyTasksFilters,
            [$admission->patient_id]
        );

        $this->assertTrue($result->first()->is($recheck));
    }
}
