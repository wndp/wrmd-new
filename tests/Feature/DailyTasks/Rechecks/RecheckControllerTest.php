<?php

namespace Tests\Feature\DailyTasks\Rechecks;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\Patient;
use App\Models\Recheck;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

#[Group('daily-tasks')]
final class RecheckControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    private $frequencyId;

    private $assignmentId;

    protected function setUp(): void
    {
        parent::setUp();

        $this->frequencyId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_FREQUENCIES,
            AttributeOptionUiBehavior::DAILY_TASK_FREQUENCY_IS_EVERY_2_DAYS
        )->attribute_option_id;

        $this->assignmentId = $this->createUiBehavior(
            AttributeOptionName::DAILY_TASK_ASSIGNMENTS,
            AttributeOptionUiBehavior::DAILY_TASK_ASSIGNMENT_IS_VETERINARIAN
        )->attribute_option_id;
    }

    public function test_un_authenticated_users_cant_store_a_recheck(): void
    {
        $patient = Patient::factory()->create();
        $this->post(route('patients.recheck.store', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_store_a_recheck(): void
    {
        $me = $this->createTeamUser();

        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->post(route('patients.recheck.store', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_a_patient_before_storing(): void
    {
        $me = $this->createTeamUser();

        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->post(route('patients.recheck.store', $patient), [
                'recheck_start_at' => '2023-03-14',
                'recheck_end_at' => '2023-03-14',
                'frequency_id' => $this->frequencyId,
                'assigned_to_id' => $this->assignmentId,
                'description' => 'foo',
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_store_a_recheck(): void
    {
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->post(route('patients.recheck.store', $admission->patient))
            ->assertInvalid([
                'recheck_start_at' => 'The recheck start at field is required.',
                'frequency_id' => 'The frequency id field is required.',
                'assigned_to_id' => 'The assigned to id field is required.',
                'description' => 'The description field is required.',
            ]);

        $this->actingAs($me->user)
            ->post(route('patients.recheck.store', $admission->patient), [
                'recheck_start_at' => 'foo',
            ])
            ->assertInvalid(['recheck_start_at' => 'The recheck start at field must be a valid date.']);

        $this->actingAs($me->user)
            ->post(route('patients.recheck.store', $admission->patient), [
                'recheck_start_at' => '2022-06-30',
                'recheck_end_at' => '2022-06-28',
            ])
            ->assertInvalid(['recheck_end_at' => 'The recheck end at field must be a date after or equal to recheck start at.']);
    }

    public function test_it_stores_a_recheck(): void
    {
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->post(route('patients.recheck.store', $admission->patient), [
                'recheck_start_at' => '2017-05-06',
                'frequency_id' => $this->frequencyId,
                'assigned_to_id' => $this->assignmentId,
                'description' => 'lorem',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('rechecks', [
            'patient_id' => $admission->patient_id,
            'recheck_start_at' => '2017-05-06',
            'recheck_end_at' => null,
            'frequency_id' => $this->frequencyId,
            'assigned_to_id' => $this->assignmentId,
            'description' => 'lorem',
        ]);
    }

    public function test_it_validates_ownership_of_a_recheck_before_updating(): void
    {
        $me = $this->createTeamUser();

        $patient = Patient::factory()->create();
        $recheck = Recheck::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->put(route('patients.recheck.update', [$patient, $recheck]), [
                'recheck_start_at' => '2023-03-14',
                'recheck_end_at' => '2023-03-14',
                'frequency_id' => $this->frequencyId,
                'assigned_to_id' => $this->assignmentId,
                'description' => 'foo',
            ])
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_a_recheck(): void
    {
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->put(route('patients.recheck.update', [$admission->patient, $recheck]))
            ->assertInvalid([
                'recheck_start_at' => 'The recheck start at field is required.',
                'frequency_id' => 'The frequency id field is required.',
                'assigned_to_id' => 'The assigned to id field is required.',
                'description' => 'The description field is required.',
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.recheck.update', [$admission->patient, $recheck]), [
                'recheck_start_at' => 'foo',
            ])
            ->assertInvalid(['recheck_start_at' => 'The recheck start at field must be a valid date.']);

        $this->actingAs($me->user)
            ->put(route('patients.recheck.update', [$admission->patient, $recheck]), [
                'recheck_start_at' => '2022-06-30',
                'recheck_end_at' => '2022-06-28',
            ])
            ->assertInvalid(['recheck_end_at' => 'The recheck end at field must be a date after or equal to recheck start at.']);
    }

    public function test_it_updates_a_recheck(): void
    {
        $me = $this->createTeamUser();

        $admission = $this->createCase($me->team);
        $recheck = Recheck::factory()->create(['patient_id' => $admission->patient]);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_DAILY_TASKS->value);

        $this->actingAs($me->user)
            ->from(route('dashboard'))
            ->put(route('patients.recheck.update', [$admission->patient, $recheck]), [
                'recheck_start_at' => '2022-06-30',
                'recheck_end_at' => '2022-06-30',
                'frequency_id' => $this->frequencyId,
                'assigned_to_id' => $this->assignmentId,
                'description' => 'lorem',
            ])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('rechecks', [
            'id' => $recheck->id,
            'patient_id' => $admission->patient_id,
            'recheck_start_at' => '2022-06-30',
            'recheck_end_at' => '2022-06-30',
            'frequency_id' => $this->frequencyId,
            'assigned_to_id' => $this->assignmentId,
            'description' => 'lorem',
        ]);
    }
}
