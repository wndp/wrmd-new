<?php

namespace Tests\Unit\Models;

use App\Models\Media;
use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Testing\File;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Traits\Assertions;

final class MediaTest extends TestCase
{
    use Assertions;
    use RefreshDatabase;

    public function test_a_media_has_an_appended_preview_url_attribute(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        $media = Patient::factory()->create()->addMedia($file)
            ->preservingOriginal()
            ->toMediaCollection('documents');

        $this->assertStringEndsWith('/conversions/avatar-preview.jpg', $media->getUrl('preview'));
    }

    public function test_a_media_has_an_appended_medium_url_attribute(): void
    {
        $file = UploadedFile::fake()->image('avatar.jpg');

        $media = Patient::factory()->create()->addMedia($file)
            ->preservingOriginal()
            ->toMediaCollection('documents');

        $this->assertStringEndsWith('/conversions/avatar-medium.jpg', $media->getUrl('medium'));
    }

    public function test_a_media_is_revisionable(): void
    {
        activity()->enableLogging();

        $this->assertRevisionRecorded(
            Media::factory()->create(),
            'created'
        );
    }

    public function test_if_a_medias_patient_is_locked_then_it_can_not_be_updated(): void
    {
        $patient = Patient::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');

        $media = $patient->addMedia($file)
            ->preservingOriginal()
            ->usingName('OLD')
            ->withCustomProperties(array_merge(
                ['foo' => 'OLD'],
            ))
            ->toMediaCollection('documents');

        $patient->locked_at = Carbon::now();
        $patient->save();
        $media->model->refresh();

        // Cant update
        $media->setCustomProperty('foo', 'NEW')->save();
        $media->update(['name' => 'NEW']);
        $this->assertEquals('OLD', $media->fresh()->getCustomProperty('foo'));
        $this->assertEquals('OLD', $media->fresh()->name);

        // Cant save
        $media->name = 'NEW';
        $media->save();
        $this->assertEquals('OLD', $media->fresh()->name);
    }

    public function test_if_a_medias_patient_is_locked_then_it_can_not_be_created(): void
    {
        $patient = Patient::factory()->create(['locked_at' => Carbon::now()]);
        $file = UploadedFile::fake()->image('avatar.jpg');

        $media = $patient->addMedia($file)
            ->preservingOriginal()
            ->toMediaCollection('documents');

        $this->assertFalse($media->exists);
    }

    public function test_if_a_medias_patient_is_locked_then_it_can_not_be_deleted(): void
    {
        $patient = Patient::factory()->create();
        $file = UploadedFile::fake()->image('avatar.jpg');

        $media = $patient->addMedia($file)
            ->preservingOriginal()
            ->toMediaCollection('documents');

        $patient->locked_at = Carbon::now();
        $patient->save();
        $media->model->refresh();
        $media->delete();
        $this->assertDatabaseHas('media', ['id' => $media->id]);
    }

    public function test_when_a_patient_is_replicated_so_are_the_media(): void
    {
        Storage::fake('s3');

        config()->set('media-library.image_driver', 'gd');
        config()->set('filesystems.disks.s3', [
            'driver' => 'local',
            'root' => base_path('tests/storage'),
        ]);

        $patient = tap(Patient::factory()->create(), function ($patient) {
            $patient->addMedia(File::image('photo1.jpg'))->toMediaCollection();
            $patient->addMedia(File::image('photo2.jpg'))->toMediaCollection();
        });

        $newPatient = $patient->clone();

        $this->assertCount(2, $patient->getMedia());
        $this->assertCount(2, $newPatient->getMedia());
        $this->assertFileExists($patient->getMedia()->first()->getPath());
        $this->assertFileExists($newPatient->getMedia()->first()->getPath());
        $this->assertFileExists($patient->getMedia()->last()->getPath());
        $this->assertFileExists($newPatient->getMedia()->last()->getPath());
    }
}
