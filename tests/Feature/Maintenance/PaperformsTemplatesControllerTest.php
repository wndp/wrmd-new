<?php

namespace Tests\Feature\Extensions\Paperforms;

use App\Enums\Extension;
use App\Enums\SettingKey;
use App\Support\ExtensionManager;
use App\Support\Wrmd;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\Support\AssistsWithAuthentication;
use Tests\Support\AssistsWithCases;
use Tests\TestCase;
use Tests\Traits\Assertions;
use Tests\Traits\CreateCase;
use Tests\Traits\CreatesTeamUser;

class PaperformsTemplatesControllerTest extends TestCase
{
    use Assertions;
    use CreateCase;
    use CreatesTeamUser;
    use RefreshDatabase;

    #[Test]
    public function unAuthenticatedUsersCantAccessPaperForms(): void
    {
        $this->get(route('maintenance.paper_forms.index'))->assertRedirect('login');
    }

    #[Test]
    public function itListsTheAvailablePaperFormTeplates()
    {
        $me = $this->createTeamUser();
        ExtensionManager::activate($me->team, Extension::PAPER_FORMS);

        $this->actingAs($me->user)->get(route('maintenance.paper_forms.index'))
            ->assertOk()
            ->assertInertia(function ($page) {
                $page->component('Maintenance/PaperForms/Index')
                    ->has('templates');
            });
    }

    #[Test]
    public function aNameIsRequiredToStoreATempalte()
    {
        $me = $this->createTeamUser();
        ExtensionManager::activate($me->team, Extension::PAPER_FORMS);

        $this->actingAs($me->user)->post(route('maintenance.paper_forms.store'))
            ->assertInvalid(['name' => 'The name field is required.']);
    }

    #[Test]
    public function aValidFileIsRequiredToStoreATempalte()
    {
        Storage::fake('s3');

        $me = $this->createTeamUser();
        ExtensionManager::activate($me->team, Extension::PAPER_FORMS);

        $this->actingAs($me->user)->post(route('maintenance.paper_forms.store'))
            ->assertInvalid(['template' => 'The template field is required.']);

        $this->actingAs($me->user)->post(route('maintenance.paper_forms.store'), [
            'template' => UploadedFile::fake()->image('avatar.jpg'),
        ])
            ->assertInvalid(['template' => 'The template field must be a file of type: pdf.']);

        $this->actingAs($me->user)->post(route('maintenance.paper_forms.store'), [
            'template' => UploadedFile::fake()->create('template.pdf', 6000),
        ])
            ->assertInvalid(['template' => 'The template field must not be greater than 5000 kilobytes.']);
    }

    #[Test]
    public function aNewPaperFormTemplateIsSavedToStorage()
    {
        Storage::fake('s3');

        $me = $this->createTeamUser();
        ExtensionManager::activate($me->team, Extension::PAPER_FORMS);

        $this->actingAs($me->user)->post(route('maintenance.paper_forms.store'), [
            'name' => 'foo form',
            'template' => UploadedFile::fake()->create('template.pdf'),
        ])
            ->assertRedirect(route('maintenance.paper_forms.index'));

        $paperFormTemplates = Wrmd::settings(SettingKey::PAPER_FORM_TEMPLATES);

        Storage::disk('s3')->assertExists($paperFormTemplates[0]['path']);

        $this->assertTeamHasSetting($me->team, SettingKey::PAPER_FORM_TEMPLATES, [[
            'name' => 'foo form',
            'slug' => 'foo-form',
            'path' => $paperFormTemplates[0]['path'],
            'created_at' => $paperFormTemplates[0]['created_at'],
        ]]);
    }

    #[Test]
    public function newPaperFormTemplatesArePushedToTheEndOfTheSettingStorage()
    {
        Storage::fake('s3');

        $me = $this->createTeamUser();
        ExtensionManager::activate($me->team, Extension::PAPER_FORMS);

        $this->setSetting($me->team, SettingKey::PAPER_FORM_TEMPLATES, [[
            'name' => 'foo form',
            'slug' => 'foo-form',
            'path' => 'fakepath',
            'created_at' => 'fake',
        ]]);

        $this->actingAs($me->user)->post(route('maintenance.paper_forms.store'), [
            'name' => 'bar form',
            'template' => UploadedFile::fake()->create('template.pdf'),
        ])
            ->assertRedirect(route('maintenance.paper_forms.index'));

        $paperFormTemplates = Wrmd::settings(SettingKey::PAPER_FORM_TEMPLATES);

        Storage::disk('s3')->assertExists($paperFormTemplates[1]['path']);

        $this->assertTeamHasSetting($me->team, SettingKey::PAPER_FORM_TEMPLATES, [
            [
                'name' => 'foo form',
                'slug' => 'foo-form',
                'path' => 'fakepath',
                'created_at' => 'fake',
            ],
            [
                'name' => 'bar form',
                'slug' => 'bar-form',
                'path' => $paperFormTemplates[1]['path'],
                'created_at' => $paperFormTemplates[1]['created_at'],
            ],
        ]);
    }

    #[Test]
    public function aPaperFormTemplateIsDeletedFromStorage()
    {
        $this->withoutExceptionHandling();
        Storage::fake('s3');

        $me = $this->createTeamUser();
        ExtensionManager::activate($me->team, Extension::PAPER_FORMS);

        $this->setSetting($me->team, SettingKey::PAPER_FORM_TEMPLATES, [[
            'name' => 'foo form',
            'slug' => 'foo-form',
            'path' => UploadedFile::fake()->create('template.pdf')->store($me->team->id.'/reports/paperforms', 's3'),
            'created_at' => 'fake',
        ]]);

        $paperformsTemplates = $me->team->settingsStore()->get(SettingKey::PAPER_FORM_TEMPLATES);

        $this->actingAs($me->user)->delete(route('maintenance.paper_forms.destroy', 'foo-form'), [
            'name' => 'bar form',
            'template' => UploadedFile::fake()->create('template.pdf'),
        ])
            ->assertRedirect(route('maintenance.paper_forms.index'));

        Storage::disk('s3')->assertMissing($paperformsTemplates[0]['path']);

        $this->assertTeamMissingSetting($me->team, SettingKey::PAPER_FORM_TEMPLATES);
    }
}
