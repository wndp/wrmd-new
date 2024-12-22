<?php

namespace Tests\Unit\Support;

use App\Enums\Extension;
use App\Models\Team;
use App\Support\ExtensionManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;
use Tests\Traits\AssistsWithExtensions;

final class ExtensionManagerTest extends TestCase
{
    use AssistsWithExtensions;
    use RefreshDatabase;

    public function test_it_gets_the_activated_extensions(): void
    {
        $team = $this->activateExtension(Extension::ATTACHMENTS);

        $result = ExtensionManager::getActivated($team->fresh());

        $this->assertCount(1, $result);
    }

    public function test_it_activates_an_extension(): void
    {
        $team = $this->activateExtension(Extension::ATTACHMENTS);

        $result = ExtensionManager::getActivated($team->fresh());

        $this->assertEquals('Attachments', $result[0]->extension->label());
    }

    public function test_activated_extensions_can_not_be_activated_multiple_times(): void
    {
        $team = Team::factory()->create();

        $this->assertCount(0, ExtensionManager::getActivated($team));

        ExtensionManager::activate($team, Extension::ATTACHMENTS);
        $this->assertCount(1, ExtensionManager::getActivated($team->fresh()));

        ExtensionManager::activate($team, Extension::ATTACHMENTS);
        $this->assertCount(1, ExtensionManager::getActivated($team->fresh()));
    }

    public function test_dependent_extensions_are_activated_when_an_extension_is_activated(): void
    {
        $team = Team::factory()->create();
        ExtensionManager::activate($team, Extension::OIL_SPILL_PROCESSING);

        $result = ExtensionManager::getActivated($team->fresh());

        $this->assertSame(
            [
                Extension::ATTACHMENTS,
                Extension::BANDING_MORPHOMETRICS,
                Extension::OIL_SPILL_PROCESSING,
            ],
            $result->pluck('extension')->sortBy('value')->values()->toArray()
        );
    }

    public function test_it_deactivates_an_extension(): void
    {
        $team = $this->activateExtension(Extension::ATTACHMENTS);

        ExtensionManager::deactivate($team, Extension::ATTACHMENTS);

        $result = ExtensionManager::getActivated($team->fresh());

        $this->assertCount(0, $result);
    }

    public function test_dependent_extensions_are_deactivated_when_a_parent_is_deactivated(): void
    {
        $team = Team::factory()->create();
        ExtensionManager::activate($team, Extension::OIL_SPILL_PROCESSING);

        ExtensionManager::deactivate($team->fresh(), Extension::OIL_SPILL_PROCESSING);

        $result = ExtensionManager::getActivated($team);

        $this->assertCount(0, $result);
    }

    public function test_a_dependent_extension_can_not_be_deactivated_when_its_parent_is_activated(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Can not deactivate extension because of active parent extension: '.Extension::OIL_SPILL_PROCESSING->label());

        $team = Team::factory()->create();
        ExtensionManager::activate($team, Extension::OIL_SPILL_PROCESSING);

        ExtensionManager::deactivate($team->fresh(), Extension::ATTACHMENTS);
    }

    public function test_it_determines_if_the_extension_is_activated(): void
    {
        $team = Team::factory()->create();

        $this->assertFalse(ExtensionManager::isActivated(Extension::ATTACHMENTS, $team));
        Cache::clear();

        $activated = $this->activateExtension(Extension::ATTACHMENTS, $team);

        $this->assertTrue(ExtensionManager::isActivated(Extension::ATTACHMENTS, $team->fresh()));
    }
}
