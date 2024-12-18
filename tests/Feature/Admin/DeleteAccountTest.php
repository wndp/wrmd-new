<?php

namespace Tests\Feature\Admin;

use App\Enums\Extension;
use App\Enums\Role;
use App\Jobs\DeleteTeam;
use App\Models\Banding;
use App\Models\CareLog;
use App\Models\Communication;
use App\Models\CustomField;
use App\Models\Donation;
use App\Models\Exam;
use App\Models\ExpenseCategory;
use App\Models\ExpenseTransaction;
use App\Models\FailedImport;
use App\Models\Formula;
use App\Models\Incident;
use App\Models\LabCbcResult;
use App\Models\LabChemistryResult;
use App\Models\LabCytologyResult;
use App\Models\LabFecalResult;
use App\Models\LabReport;
use App\Models\LabToxicologyResult;
use App\Models\LabUrinalysisResult;
use App\Models\Location;
use App\Models\Morphometric;
use App\Models\Necropsy;
use App\Models\NutritionPlan;
use App\Models\PatientLocation;
use App\Models\Person;
use App\Models\Prescription;
use App\Models\Recheck;
use App\Models\Setting;
use App\Models\Team;
use App\Models\User;
use App\Models\Veterinarian;
use App\Support\ExtensionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\CreateCase;

final class DeleteAccountTest extends TestCase
{
    use CreateCase;
    use RefreshDatabase;

    #[Test]
    public function aTeamsVeterinariansAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $veterinarians = Veterinarian::factory()->count(2)->create(['team_id' => $team->id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('veterinarians', ['id' => $veterinarians->first()->id]);
        $this->assertDatabaseMissing('veterinarians', ['id' => $veterinarians->last()->id]);
    }

    #[Test]
    public function aTeamsSettingsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $settings = Setting::factory()->count(2)->create(['team_id' => $team->id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('settings', ['id' => $settings->first()->id]);
        $this->assertDatabaseMissing('settings', ['id' => $settings->last()->id]);
    }

    // #[Test]
    // public function aTeamsRepliesAreDeletedBeforeDeletingTheTeam(): void
    // {
    //     $team = Team::factory()->create();
    //     $reply = Reply::factory()->create(['team_id' => $team->id]);

    //     DeleteTeam::dispatch($team);

    //     $this->assertDatabaseMissing('replies', ['team_id' => $reply->team_id]);
    // }

    // #[Test]
    // public function aTeamsPrivateThreadsAreDeletedBeforeDeletingTheTeam(): void
    // {
    //     $teams = Team::factory()->count(2)->create();
    //     $thread = Thread::factory()->create(['team_id' => $teams->first()->id]);

    //     $thread->privateBetween()->sync($teams->pluck('id'));

    //     DeleteTeam::dispatch($teams->first());

    //     $this->assertDatabaseMissing('private_thread_participants', ['team_id' => $teams->first()->id]);
    //     $this->assertDatabaseMissing('private_thread_participants', ['team_id' => $teams->last()->id]);
    // }

    // #[Test]
    // public function aTeamsThreadsAreDeletedBeforeDeletingTheTeam(): void
    // {
    //     $team = Team::factory()->create();
    //     $thread = Thread::factory()->create(['team_id' => $team->id]);

    //     DeleteTeam::dispatch($team);

    //     $this->assertDatabaseMissing('threads', ['team_id' => $thread->team_id]);
    // }

    // #[Test]
    // public function allRepliesToAnAccountsThreadsAreDeletedBeforeDeletingTheTeam(): void
    // {
    //     $team = Team::factory()->create();
    //     $otherAccount = Team::factory()->create();
    //     $thread = Thread::factory()->create(['team_id' => $team->id]);
    //     $reply = Reply::factory()->create(['team_id' => $team->id, 'thread_id' => $thread->id]);
    //     $otherReply = Reply::factory()->create(['team_id' => $otherAccount->id, 'thread_id' => $thread->id]);

    //     DeleteTeam::dispatch($team);

    //     $this->assertDatabaseMissing('threads', ['team_id' => $thread->team_id]);
    // }

    #[Test]
    public function aTeamsActivatedExtensionsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();

        ExtensionManager::activate($team, Extension::EXPENSES);
        ExtensionManager::activate($team, Extension::QUICK_ADMIT);

        DeleteTeam::dispatch($team);

        $this->assertFalse(ExtensionManager::isActivated(Extension::EXPENSES, $team));
        $this->assertFalse(ExtensionManager::isActivated(Extension::QUICK_ADMIT, $team));
    }

    #[Test]
    public function aTeamsFormulasAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        Formula::factory()->create(['team_id' => $team->id]);

        $this->assertDatabaseHas('formulas', ['team_id' => $team->id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('formulas', ['team_id' => $team->id]);
    }

    #[Test]
    public function aTeamsCustomFieldsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $customField = CustomField::factory()->create(['team_id' => $team->id]);

        $this->assertDatabaseHas('custom_fields', ['team_id' => $customField->team_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('custom_fields', ['team_id' => $customField->team_id]);
    }

    #[Test]
    public function aTeamsFailedImportsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $failedImport = FailedImport::factory()->create(['team_id' => $team->id]);

        $this->assertDatabaseHas('failed_imports', ['team_id' => $failedImport->team_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('failed_imports', ['team_id' => $failedImport->team_id]);
    }

    #[Test]
    public function aTeamsExpenseCategoriesAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $category = ExpenseCategory::factory()->create(['team_id' => $team->id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('expense_categories', ['team_id' => $category->team_id]);
    }

    #[Test]
    public function aTeamsUsersAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $user1 = User::factory()->create()->joinTeam($team, Role::USER);
        $user2 = User::factory()->create()->joinTeam($team, Role::USER);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('team_user', ['user_id' => $user1->id]);
        $this->assertDatabaseMissing('team_user', ['user_id' => $user2->id]);
        $this->assertDatabaseMissing('users', ['id' => $user1->user_id]);
        $this->assertDatabaseMissing('users', ['id' => $user2->user_id]);
    }

    #[Test]
    public function aTeamsAdmissionsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $this->createCase($team);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('admissions', ['team_id' => $team->id]);
    }

    #[Test]
    public function aTeamsPatientsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('patients', ['id' => $admission->patient_id]);
    }

    #[Test]
    public function aTeamsPatientsExamsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        Exam::factory()->create(['patient_id' => $admission->patient_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('exams', ['patient_id' => $admission->patient_id]);
    }

    #[Test]
    public function aTeamsPatientsLocationsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        PatientLocation::factory()->create(['patient_id' => $admission->patient_id]);

        $this->assertDatabaseHas('patient_locations', ['patient_id' => $admission->patient_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('patient_locations', ['patient_id' => $admission->patient_id]);
    }

    #[Test]
    public function aTeamsPeopleAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $this->createCase($team);
        Person::factory()->create(['team_id' => $team->id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('people', ['team_id' => $team->id]);
    }

    #[Test]
    public function aTeamsPeoplesDonationsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $this->createCase($team);
        $person = Person::factory()->create(['team_id' => $team->id]);
        $donation = Donation::factory()->create(['person_id' => $person->id]);

        $this->assertDatabaseHas('donations', ['id' => $donation->id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('donations', ['id' => $donation->id]);
    }

    #[Test]
    public function aTeamsLocationsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        Location::factory()->create(['team_id' => $team->id]);

        $this->assertDatabaseHas('locations', ['team_id' => $team->id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('locations', ['team_id' => $team->id]);
    }

    #[Test]
    public function aTeamsLabReportsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        LabReport::factory()->create(['patient_id' => $admission->patient_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('lab_reports', ['patient_id' => $admission->patient_id]);
    }

    #[Test]
    public function aTeamsFecalLabResultsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        $labReport = LabReport::factory()->for(LabFecalResult::factory(), 'labResult')->create([
            'patient_id' => $admission->patient_id,
        ]);

        $labResultId = $labReport->labResult->id;

        $this->assertDatabaseHas('lab_fecal_results', ['id' => $labResultId]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('lab_fecal_results', ['id' => $labResultId]);
    }

    #[Test]
    public function aTeamsCbcLabResultsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        $labReport = LabReport::factory()->for(LabCbcResult::factory(), 'labResult')->create([
            'patient_id' => $admission->patient_id,
        ]);

        $labResultId = $labReport->labResult->id;

        $this->assertDatabaseHas('lab_cbc_results', ['id' => $labResultId]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('lab_cbc_results', ['id' => $labResultId]);
    }

    #[Test]
    public function aTeamsChemistryLabResultsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        $labReport = LabReport::factory()->for(LabChemistryResult::factory(), 'labResult')->create([
            'patient_id' => $admission->patient_id,
        ]);

        $labResultId = $labReport->labResult->id;

        $this->assertDatabaseHas('lab_chemistry_results', ['id' => $labResultId]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('lab_chemistry_results', ['id' => $labResultId]);
    }

    #[Test]
    public function aTeamsCytologyLabResultsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        $labReport = LabReport::factory()->for(LabCytologyResult::factory(), 'labResult')->create([
            'patient_id' => $admission->patient_id,
        ]);

        $labResultId = $labReport->labResult->id;

        $this->assertDatabaseHas('lab_cytology_results', ['id' => $labResultId]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('lab_cytology_results', ['id' => $labResultId]);
    }

    #[Test]
    public function aTeamsToxicologyLabResultsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        $labReport = LabReport::factory()->for(LabToxicologyResult::factory(), 'labResult')->create([
            'patient_id' => $admission->patient_id,
        ]);

        $labResultId = $labReport->labResult->id;

        $this->assertDatabaseHas('lab_toxicology_results', ['id' => $labResultId]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('lab_toxicology_results', ['id' => $labResultId]);
    }

    #[Test]
    public function aTeamsUrinalysisLabResultsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        $labReport = LabReport::factory()->for(LabUrinalysisResult::factory(), 'labResult')->create([
            'patient_id' => $admission->patient_id,
        ]);

        $labResultId = $labReport->labResult->id;

        $this->assertDatabaseHas('lab_urinalysis_results', ['id' => $labResultId]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('lab_urinalysis_results', ['id' => $labResultId]);
    }

    #[Test]
    public function aTeamsPrescriptionsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        Prescription::factory()->create(['patient_id' => $admission->patient_id]);

        $this->assertDatabaseHas('prescriptions', ['patient_id' => $admission->patient_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('prescriptions', ['patient_id' => $admission->patient_id]);
    }

    #[Test]
    public function aTeamsRechecksAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        Recheck::factory()->create(['patient_id' => $admission->patient_id]);

        $this->assertDatabaseHas('rechecks', ['patient_id' => $admission->patient_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('rechecks', ['patient_id' => $admission->patient_id]);
    }

    #[Test]
    public function aTeamsNutritionPlansAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        NutritionPlan::factory()->create(['patient_id' => $admission->patient_id]);

        $this->assertDatabaseHas('nutrition_plans', ['patient_id' => $admission->patient_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('nutrition_plans', ['patient_id' => $admission->patient_id]);
    }

    #[Test]
    public function aTeamsCareLogsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        CareLog::factory()->create(['patient_id' => $admission->patient_id]);

        $this->assertDatabaseHas('care_logs', ['patient_id' => $admission->patient_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('care_logs', ['patient_id' => $admission->patient_id]);
    }

    #[Test]
    public function aTeamsExpenseTransactionsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        ExpenseTransaction::factory()->create(['patient_id' => $admission->patient_id]);

        $this->assertDatabaseHas('expense_transactions', ['patient_id' => $admission->patient_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('expense_transactions', ['patient_id' => $admission->patient_id]);
    }

    #[Test]
    public function aTeamsBandingsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        Banding::factory()->create(['patient_id' => $admission->patient_id]);

        $this->assertDatabaseHas('bandings', ['patient_id' => $admission->patient_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('bandings', ['patient_id' => $admission->patient_id]);
    }

    #[Test]
    public function aTeamsMorphometricsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        Morphometric::factory()->create(['patient_id' => $admission->patient_id]);

        $this->assertDatabaseHas('morphometrics', ['patient_id' => $admission->patient_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('morphometrics', ['patient_id' => $admission->patient_id]);
    }

    #[Test]
    public function aTeamsNecropsiesAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        Necropsy::factory()->create(['patient_id' => $admission->patient_id]);

        $this->assertDatabaseHas('necropsies', ['patient_id' => $admission->patient_id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('necropsies', ['patient_id' => $admission->patient_id]);
    }

    #[Test]
    public function aTeamsIncidentsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();
        $admission = $this->createCase($team);
        Incident::factory()->create([
            'team_id' => $team->id,
            'patient_id' => $admission->patient_id,
        ]);

        $this->assertDatabaseHas('incidents', ['team_id' => $team->id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('incidents', ['team_id' => $team->id]);
    }

    #[Test]
    public function aTeamsIncidentCommunicationsAreDeletedBeforeDeletingTheTeam(): void
    {
        $team = Team::factory()->create();

        $communication = Communication::factory()->for(Incident::factory()->create(['team_id' => $team->id]))->create();

        $this->assertDatabaseHas('communications', ['id' => $communication->id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('communications', ['id' => $communication->id]);
    }

    #[Test]
    public function aTeamIsDeleted(): void
    {
        $team = Team::factory()->create();

        $this->assertDatabaseHas('teams', ['id' => $team->id]);

        DeleteTeam::dispatch($team);

        $this->assertDatabaseMissing('teams', ['id' => $team->id]);
    }
}
