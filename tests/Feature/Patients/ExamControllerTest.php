<?php

namespace Tests\Feature\Patients;

use App\Enums\Ability;
use App\Enums\AttributeOptionName;
use App\Enums\AttributeOptionUiBehavior;
use App\Models\AttributeOptionUiBehavior as AttributeOptionUiBehaviorModel;
use App\Models\Exam;
use App\Models\Patient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Silber\Bouncer\BouncerFacade;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;
use Tests\Traits\CreatesUiBehavior;

final class ExamControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use CreatesUiBehavior;
    use RefreshDatabase;

    public function test_un_authenticated_users_cant_access_exams(): void
    {
        $patient = Patient::factory()->create();
        $this->get(route('patients.exam.index', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_exams(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->get(route('patients.exam.index', $patient))->assertForbidden();
    }

    public function test_it_displays_the_exam_index_view(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);

        $this->actingAs($me->user)->get(route('patients.exam.index'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Exams/Index')
                    ->has('exams')
            );
    }

    public function test_un_authenticated_users_cant_create_exams(): void
    {
        $patient = Patient::factory()->create();
        $this->get(route('patients.exam.create', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_create_exams(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        $this->actingAs($me->user)->get(route('patients.exam.create', $patient))->assertForbidden();
    }

    public function test_it_displays_the_exam_create_view(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXAMS->value);

        $this->actingAs($me->user)->get(route('patients.exam.create'))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Exams/Create')
            );
    }

    public function test_un_authenticated_users_cant_store_an_exam(): void
    {
        $patient = Patient::factory()->create();
        $this->post(route('patients.exam.store', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_store_an_exam(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        $this->actingAs($me->user)->post(route('patients.exam.store', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_storing_the_exam(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXAMS->value);

        $this->actingAs($me->user)
            ->post(route('patients.exam.store', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_store_the_exam(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2017-02-13']);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXAMS->value);

        $this->actingAs($me->user)
            ->post(route('patients.exam.store', $admission->patient))
            ->assertInvalid([
                'exam_type_id' => 'The exam type field is required.',
                'examined_at' => 'The examined at date field is required.',
                'examiner' => 'The examiner field is required.',
            ]);

        $this->actingAs($me->user)
            ->post(route('patients.exam.store', $admission->patient), [
                'examined_at' => 'foo',
                'exam_type_id' => 123,
            ])
            ->assertInvalid([
                'examined_at' => 'The examined at date is not a valid date.',
                'exam_type_id' => 'The selected exam type is invalid.',
            ]);

        $intakeExamTypeId = $this->createUiBehavior(AttributeOptionName::EXAM_TYPES, AttributeOptionUiBehavior::EXAM_TYPE_IS_INTAKE);
        $releaseExamTypeId = $this->createUiBehavior(AttributeOptionName::EXAM_TYPES, AttributeOptionUiBehavior::EXAM_TYPE_IS_RELEASE);

        Exam::factory()->create(['patient_id' => $admission->patient_id, 'exam_type_id' => $intakeExamTypeId]);
        Exam::factory()->create(['patient_id' => $admission->patient_id, 'exam_type_id' => $releaseExamTypeId]);

        $this->actingAs($me->user)
            ->post(route('patients.exam.store', $admission->patient), [
                'examined_at' => '2017-02-13 08:43:00',
                'examiner' => 'Jim Halpert',
                'exam_type_id' => $intakeExamTypeId,
            ])
            ->assertInvalid(['exam_type_id' => 'The selected exam type is invalid.']);

        $this->actingAs($me->user)
            ->post(route('patients.exam.store', $admission->patient), [
                'examined_at' => '2017-02-13 08:43:00',
                'examiner' => 'Jim Halpert',
                'exam_type_id' => $releaseExamTypeId,
            ])
            ->assertInvalid(['exam_type_id' => 'The selected exam type is invalid.']);
    }

    public function test_it_stores_an_exam(): void
    {
        $intakeExamTypeId = $this->createUiBehavior(AttributeOptionName::EXAM_TYPES, AttributeOptionUiBehavior::EXAM_TYPE_IS_INTAKE);

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2017-02-13']);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXAMS->value);

        $this->actingAs($me->user)
            ->post(route('patients.exam.store', $admission->patient), [
                'examined_at' => '2017-02-13 08:43:00',
                'exam_type_id' => $intakeExamTypeId,
                'examiner' => 'Jim Halpert',
                // ... other attributes are tested in the Dusk DailyExamsTest
            ])
            ->assertRedirect(route('patients.exam.index', ['y' => now()->format('Y'), 'c' => 1]));

        $this->assertDatabaseHas('exams', [
            'patient_id' => $admission->patient_id,
            'date_examined_at' => '2017-02-13',
            'time_examined_at' => '16:43:00',
            'exam_type_id' => $intakeExamTypeId,
            'examiner' => 'Jim Halpert',
        ]);
    }

    public function test_un_authenticated_users_cant_edit_an_exam(): void
    {
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        $this->get(route('patients.exam.edit', $exam))->assertRedirect('login');
    }

    public function test_it_validates_ownership_of_the_patient_before_editing_the_exam(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXAMS->value);

        $this->actingAs($me->user)
            ->get(route('patients.exam.edit', $exam))
            ->assertOwnershipValidationError();
    }

    public function test_it_displays_the_exam_edit_view(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $exam = Exam::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXAMS->value);

        $this->actingAs($me->user)->get(route('patients.exam.edit', $exam))
            ->assertOk()
            ->assertInertia(
                fn ($page) => $page->component('Patients/Exams/Edit')
                    ->has('exam')
                    ->where('exam.id', $exam->id)
            );
    }

    public function test_un_authenticated_users_cant_update_an_exam(): void
    {
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        $this->put(route('patients.exam.update', [$patient, $exam]))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_update_an_exam(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        $this->actingAs($me->user)->put(route('patients.exam.update', [$patient, $exam]))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_exam_before_updating_it(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXAMS->value);

        $this->actingAs($me->user)
            ->put(route('patients.exam.update', [$patient, $exam]))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_exam(): void
    {
        $intakeExamTypeId = $this->createUiBehavior(AttributeOptionName::EXAM_TYPES, AttributeOptionUiBehavior::EXAM_TYPE_IS_INTAKE);
        $releaseExamTypeId = $this->createUiBehavior(AttributeOptionName::EXAM_TYPES, AttributeOptionUiBehavior::EXAM_TYPE_IS_RELEASE);

        AttributeOptionUiBehaviorModel::factory()->create([
            'attribute_option_id' => $intakeExamTypeId,
            'behavior' => AttributeOptionUiBehavior::EXAM_TYPE_CAN_ONLY_OCCUR_ONCE->value,
        ]);

        AttributeOptionUiBehaviorModel::factory()->create([
            'attribute_option_id' => $releaseExamTypeId,
            'behavior' => AttributeOptionUiBehavior::EXAM_TYPE_CAN_ONLY_OCCUR_ONCE->value,
        ]);

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2017-02-13']);
        $exam = Exam::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXAMS->value);

        $this->actingAs($me->user)
            ->put(route('patients.exam.update', [$admission->patient, $exam]))
            ->assertInvalid([
                'exam_type_id' => 'The exam type field is required.',
                'examined_at' => 'The examined at date field is required.',
                'examiner' => 'The examiner field is required.',
            ]);

        $this->actingAs($me->user)
            ->put(route('patients.exam.update', [$admission->patient, $exam]), [
                'examined_at' => 'foo',
                'exam_type_id' => 'xxxx',
            ])
            ->assertInvalid([
                'examined_at' => 'The examined at date is not a valid date.',
                'exam_type_id' => 'The selected exam type is invalid.',
            ]);

        Exam::factory()->create(['patient_id' => $admission->patient_id, 'exam_type_id' => $intakeExamTypeId]);
        Exam::factory()->create(['patient_id' => $admission->patient_id, 'exam_type_id' => $releaseExamTypeId]);

        $this->actingAs($me->user)
            ->put(route('patients.exam.update', [$admission->patient, $exam]), [
                'examined_at' => '2017-02-13 08:43:00',
                'examiner' => 'Jim Halpert',
                'exam_type_id' => $intakeExamTypeId,
            ])
            ->assertInvalid(['exam_type_id' => 'The exam type has already been taken.']);

        $this->actingAs($me->user)
            ->put(route('patients.exam.update', [$admission->patient, $exam]), [
                'examined_at' => '2017-02-13 08:43:00',
                'examiner' => 'Jim Halpert',
                'exam_type_id' => $releaseExamTypeId,
            ])
            ->assertInvalid(['exam_type_id' => 'The exam type has already been taken.']);
    }

    public function test_it_updates_an_exam(): void
    {
        $intakeExamTypeId = $this->createUiBehavior(AttributeOptionName::EXAM_TYPES, AttributeOptionUiBehavior::EXAM_TYPE_IS_INTAKE);

        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team, patientOverrides: ['date_admitted_at' => '2017-02-13']);
        $exam = Exam::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXAMS->value);

        $this->actingAs($me->user)
            ->from('patient/initial')
            ->put(route('patients.exam.update', [$admission->patient, $exam]), [
                'examined_at' => '2017-02-13 08:43:00',
                'exam_type_id' => $intakeExamTypeId,
                'examiner' => 'Jim Halpert',
                // ... other attributes are tested in the Dusk DailyExamsTest
            ])
            ->assertRedirect('patient/initial');

        $this->assertDatabaseHas('exams', [
            'patient_id' => $admission->patient_id,
            'date_examined_at' => '2017-02-13',
            'time_examined_at' => '16:43:00',
            'exam_type_id' => $intakeExamTypeId,
            'examiner' => 'Jim Halpert',
        ]);
    }

    ///

    public function test_un_authenticated_users_cant_delete_an_exam(): void
    {
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        $this->delete(route('patients.exam.destroy', [$patient, $exam]))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_delete_an_exam(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        $this->actingAs($me->user)->delete(route('patients.exam.destroy', [$patient, $exam]))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_exam_before_delete_it(): void
    {
        $me = $this->createTeamUser();
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXAMS->value);

        $this->actingAs($me->user)
            ->delete(route('patients.exam.destroy', [$patient, $exam]))
            ->assertOwnershipValidationError();
    }

    public function test_it_deletes_an_exam(): void
    {
        $me = $this->createTeamUser();
        $admission = $this->createCase($me->team);
        $exam = Exam::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to(Ability::VIEW_EXAMS->value);
        BouncerFacade::allow($me->user)->to(Ability::MANAGE_EXAMS->value);

        $this->actingAs($me->user)
            ->delete(route('patients.exam.destroy', [$admission->patient, $exam]))
            ->assertRedirect(route('patients.exam.index', ['y' => now()->format('Y'), 'c' => 1]));

        $this->assertSoftDeleted('exams', [
            'id' => $exam->id,
            'patient_id' => $admission->patient_id,
        ]);
    }
}
