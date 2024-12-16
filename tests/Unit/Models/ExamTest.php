<?php

namespace Tests\Unit\Models;

use App\Concerns\GetsCareLogs;
use App\Models\Exam;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreatesTeamUser;

final class ExamTest extends TestCase
{
    use Assertions;
    use CreatesTeamUser;
    use GetsCareLogs;
    use RefreshDatabase;

    #[Test]
    public function examsAreFilteredIntoTheCareLog(): void
    {
        $me = $this->createTeamUser();
        Auth::loginUsingId($me->user->id);
        $patient = Patient::factory()->create();

        Exam::factory()->create([
            'patient_id' => $patient->id,
            'date_examined_at' => '2017-04-09',
        ]);

        $logs = $this->getCareLogs(
            $patient,
            $me->user
        );

        $this->assertCount(1, $logs);
        $this->assertInstanceOf(Exam::class, $logs[0]->model);
        $this->assertEquals('2017-04-08 17:00:00', $logs[0]->logged_at_date_time->toDateTimeString());
    }

    #[Test]
    public function anExamIsRevisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Exam::factory()->create(),
            'created'
        );
    }

    #[Test]
    public function ifAnExamsPatientIsLockedThenItCanNotBeUpdated(): void
    {
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id, 'weight' => 1]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $exam->patient->refresh();

        // Cant update
        $exam->update(['weight' => 2]);
        $this->assertEquals(1, $exam->fresh()->weight);

        // Cant save
        $exam->weight = 2;
        $exam->save();
        $this->assertEquals(1, $exam->fresh()->weight);
    }

    #[Test]
    public function ifAnExamsPatientIsLockedThenItCanNotBeCreated(): void
    {
        $exam = Exam::factory()->create([
            'patient_id' => Patient::factory()->create(['locked_at' => Carbon::now()])->id,
        ]);

        $this->assertFalse($exam->exists);
    }

    #[Test]
    public function ifAnExamsPatientIsLockedThenItCanNotBeDeleted(): void
    {
        $patient = Patient::factory()->create();
        $exam = Exam::factory()->create(['patient_id' => $patient->id]);
        $patient->locked_at = Carbon::now();
        $patient->save();
        $exam->patient->refresh();

        $exam->delete();
        $this->assertDatabaseHas('exams', ['id' => $exam->id, 'deleted_at' => null]);
    }
}
