<?php

namespace Tests\Feature\DailyTasks\Rechecks;

use App\Actions\GetRecheckDailyTasks;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Enums\SettingKey;
use App\Models\Recheck;
use App\Support\DailyTasksFilters;
use App\Support\Wrmd;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class GetRecheckDailyTasksTest extends TestCase
{
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;
    use CreatesUiBehavior;

    #[Test]
    public function nothingIsReturnedIfRechecksAreNotIncluded(): void
    {
        $result = GetRecheckDailyTasks::handle(
            new DailyTasksFilters(['include' => '']),
            []
        );

        $this->assertNull($result);
    }

    #[Test]
    public function itGetsAPatientsRechecks(): void
    {
        $frequencyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS
        )->attribute_option_id;

        $me = $this->createTeamUser();

        $this->registerSettingsContainer($me->team);

        $admission = $this->createCase($me->team);

        $recheck = Recheck::factory()->create([
            'patient_id' => $admission->patient_id,
            'recheck_start_at' => Carbon::now(Wrmd::settings(SettingKey::TIMEZONE)),
            'recheck_end_at' => null,
            'frequency_id' => $frequencyId,
        ]);

        $result = GetRecheckDailyTasks::handle(
            new DailyTasksFilters(),
            [$admission->patient_id]
        );

        $this->assertTrue($result->first()->is($recheck));
    }
}
