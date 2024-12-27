<?php

namespace Tests\Feature\Patients;

use App\Domain\Patients\Exam;
use App\Domain\Patients\Patient;
use App\Domain\Taxonomy\Taxon;
use Silber\Bouncer\BouncerFacade;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;

final class ExamControllerTest extends TestCase
{
    use AssistsWithAuthentication;
    use AssistsWithCases;

    protected function setUp(): void
    {
        parent::setUp();

        Taxon::factory()->unidentified()->create();
    }

    public function test_un_authenticated_users_cant_access_exams(): void
    {
        $patient = Patient::factory()->create();
        $this->get(route('patients.exam.index', $patient))->assertRedirect('login');
    }

    public function test_un_authorized_users_cant_access_exams(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $this->actingAs($me->user)->get(route('patients.exam.index', $patient))->assertForbidden();
    }

    public function test_it_displays_the_exam_index_view(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('display-exams');

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
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('display-exams');
        $this->actingAs($me->user)->get(route('patients.exam.create', $patient))->assertForbidden();
    }

    public function test_it_displays_the_exam_create_view(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        BouncerFacade::allow($me->user)->to('display-exams');
        BouncerFacade::allow($me->user)->to('manage-exams');

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
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('display-exams');
        $this->actingAs($me->user)->post(route('patients.exam.store', $patient))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_patient_before_storing_the_exam(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        BouncerFacade::allow($me->user)->to('display-exams');
        BouncerFacade::allow($me->user)->to('manage-exams');

        $this->actingAs($me->user)
            ->post(route('patients.exam.store', $patient))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_store_the_exam(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['admitted_at' => '2017-02-13 00:00:00']);
        BouncerFacade::allow($me->user)->to('display-exams');
        BouncerFacade::allow($me->user)->to('manage-exams');

        $this->actingAs($me->user)
            ->post(route('patients.exam.store', $admission->patient))
            ->assertHasValidationError('type', 'The exam type field is required.')
            ->assertHasValidationError('examined_at', 'The examined at date field is required.')
            ->assertHasValidationError('examiner', 'The examiner field is required.');

        $this->actingAs($me->user)
            ->post(route('patients.exam.store', $admission->patient), [
                'examined_at' => 'foo',
                'type' => 'xxxx',
            ])
            ->assertHasValidationError('examined_at', 'The examined at date is not a valid date.')
            ->assertHasValidationError('type', 'The selected exam type must be in [Intake, Daily, Release].');

        Exam::factory()->create(['patient_id' => $admission->patient_id, 'type' => 'Intake']);
        Exam::factory()->create(['patient_id' => $admission->patient_id, 'type' => 'Release']);

        $this->actingAs($me->user)
            ->post(route('patients.exam.store', $admission->patient), [
                'examined_at' => '2017-02-13 08:43:00',
                'examiner' => 'Jim Halpert',
                'type' => 'Intake',
            ])
            ->assertHasValidationError('type', 'Only 1 Intake exam can be created.');

        $this->actingAs($me->user)
            ->post(route('patients.exam.store', $admission->patient), [
                'examined_at' => '2017-02-13 08:43:00',
                'examiner' => 'Jim Halpert',
                'type' => 'Release',
            ])
            ->assertHasValidationError('type', 'Only 1 Release exam can be created.');
    }

    public function test_it_stores_an_exam(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['admitted_at' => '2017-02-13 00:00:00']);
        BouncerFacade::allow($me->user)->to('display-exams');
        BouncerFacade::allow($me->user)->to('manage-exams');

        $this->actingAs($me->user)
            ->post(route('patients.exam.store', $admission->patient), [
                'examined_at' => '2017-02-13 08:43:00',
                'type' => 'Intake',
                'examiner' => 'Jim Halpert',
                // ... other attributes are tested in the Dusk DailyExamsTest
            ])
            ->assertRedirect(route('patients.exam.index', ['y' => now()->format('Y'), 'c' => 1]));

        $this->assertDatabaseHas('exams', [
            'patient_id' => $admission->patient_id,
            'examined_at' => '2017-02-13 16:43:00',
            'type' => 'Intake',
            'examiner' => 'Jim Halpert',
        ]);
    }

    public function test_un_authenticated_users_cant_edit_an_exam(): void
    {
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        $this->get(route('patients.exam.edit', [$patient, $exam]))->assertRedirect('login');
    }

    public function test_it_validates_ownership_of_the_patient_before_editing_the_exam(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to('display-exams');
        BouncerFacade::allow($me->user)->to('manage-exams');

        $this->actingAs($me->user)
            ->get(route('patients.exam.edit', [$patient, $exam]))
            ->assertOwnershipValidationError();
    }

    public function test_it_displays_the_exam_edit_view(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $exam = Exam::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to('display-exams');
        BouncerFacade::allow($me->user)->to('manage-exams');

        $this->actingAs($me->user)->get(route('patients.exam.edit', [$admission->patient, $exam]))
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
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to('display-exams');
        $this->actingAs($me->user)->put(route('patients.exam.update', [$patient, $exam]))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_exam_before_updating_it(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to('display-exams');
        BouncerFacade::allow($me->user)->to('manage-exams');

        $this->actingAs($me->user)
            ->put(route('patients.exam.update', [$patient, $exam]))
            ->assertOwnershipValidationError();
    }

    public function test_it_fails_validation_when_trying_to_update_the_exam(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['admitted_at' => '2017-02-13 00:00:00']);
        $exam = Exam::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to('display-exams');
        BouncerFacade::allow($me->user)->to('manage-exams');

        $this->actingAs($me->user)
            ->put(route('patients.exam.update', [$admission->patient, $exam]))
            ->assertHasValidationError('type', 'The exam type field is required.')
            ->assertHasValidationError('examined_at', 'The examined at date field is required.')
            ->assertHasValidationError('examiner', 'The examiner field is required.');

        $this->actingAs($me->user)
            ->put(route('patients.exam.update', [$admission->patient, $exam]), [
                'examined_at' => 'foo',
                'type' => 'xxxx',
            ])
            ->assertHasValidationError('examined_at', 'The examined at date is not a valid date.')
            ->assertHasValidationError('type', 'The selected exam type must be in [Intake, Daily, Release].');

        Exam::factory()->create(['patient_id' => $admission->patient_id, 'type' => 'Intake']);
        Exam::factory()->create(['patient_id' => $admission->patient_id, 'type' => 'Release']);

        $this->actingAs($me->user)
            ->put(route('patients.exam.update', [$admission->patient, $exam]), [
                'examined_at' => '2017-02-13 08:43:00',
                'examiner' => 'Jim Halpert',
                'type' => 'Intake',
            ])
            ->assertHasValidationError('type', 'Only 1 Intake exam can be created.');

        $this->actingAs($me->user)
            ->put(route('patients.exam.update', [$admission->patient, $exam]), [
                'examined_at' => '2017-02-13 08:43:00',
                'examiner' => 'Jim Halpert',
                'type' => 'Release',
            ])
            ->assertHasValidationError('type', 'Only 1 Release exam can be created.');
    }

    public function test_it_updates_an_exam(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id], ['admitted_at' => '2017-02-13 00:00:00']);
        $exam = Exam::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to('display-exams');
        BouncerFacade::allow($me->user)->to('manage-exams');

        $this->actingAs($me->user)
            ->from('patient/initial')
            ->put(route('patients.exam.update', [$admission->patient, $exam]), [
                'examined_at' => '2017-02-13 08:43:00',
                'type' => 'Intake',
                'examiner' => 'Jim Halpert',
                // ... other attributes are tested in the Dusk DailyExamsTest
            ])
            ->assertRedirect('patient/initial');

        $this->assertDatabaseHas('exams', [
            'patient_id' => $admission->patient_id,
            'examined_at' => '2017-02-13 16:43:00',
            'type' => 'Intake',
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
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to('display-exams');
        $this->actingAs($me->user)->delete(route('patients.exam.destroy', [$patient, $exam]))->assertForbidden();
    }

    public function test_it_validates_ownership_of_the_exam_before_delete_it(): void
    {
        $me = $this->createAccountUser();
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        BouncerFacade::allow($me->user)->to('display-exams');
        BouncerFacade::allow($me->user)->to('manage-exams');

        $this->actingAs($me->user)
            ->delete(route('patients.exam.destroy', [$patient, $exam]))
            ->assertOwnershipValidationError();
    }

    public function test_it_deletes_an_exam(): void
    {
        $me = $this->createAccountUser();
        $admission = $this->createCase(['account_id' => $me->account->id]);
        $exam = Exam::factory()->create(['patient_id' => $admission->patient_id]);
        BouncerFacade::allow($me->user)->to('display-exams');
        BouncerFacade::allow($me->user)->to('manage-exams');

        $this->actingAs($me->user)
            ->delete(route('patients.exam.destroy', [$admission->patient, $exam]))
            ->assertRedirect(route('patients.exam.index', ['y' => now()->format('Y'), 'c' => 1]));

        $this->assertSoftDeleted('exams', [
            'id' => $exam->id,
            'patient_id' => $admission->patient_id,
        ]);
    }
}
