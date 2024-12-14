<?php

namespace Tests\Unit\Support;

use App\Enums\Extension;
use App\Models\Team;
use App\Support\ExtensionManager;
use DirectoryIterator;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;
use Tests\Traits\AssistsWithExtensions;

final class ExtensionManagerTest extends TestCase
{
    use RefreshDatabase;
    use AssistsWithExtensions;

    #[Test]
    public function itGetsTheActivatedExtensions(): void
    {
        $team = $this->activateExtension(Extension::ATTACHMENTS);

        $result = ExtensionManager::getActivated($team->fresh());

        $this->assertCount(1, $result);
    }

    #[Test]
    public function itActivatesAnExtension(): void
    {
        $team = $this->activateExtension(Extension::ATTACHMENTS);

        $result = ExtensionManager::getActivated($team->fresh());

        $this->assertEquals('Attachments', $result[0]->extension->label());
    }

    #[Test]
    public function activatedExtensionsCanNotBeActivatedMultipleTimes(): void
    {
        $team = Team::factory()->create();

        $this->assertCount(0, ExtensionManager::getActivated($team));

        ExtensionManager::activate($team, Extension::ATTACHMENTS);
        $this->assertCount(1, ExtensionManager::getActivated($team->fresh()));

        ExtensionManager::activate($team, Extension::ATTACHMENTS);
        $this->assertCount(1, ExtensionManager::getActivated($team->fresh()));
    }

    #[Test]
    public function dependentExtensionsAreActivatedWhenAnExtensionIsActivated(): void
    {
        $team = Team::factory()->create();
        ExtensionManager::activate($team, Extension::OIL_SPILL_PROCESSING);

        $result = ExtensionManager::getActivated($team->fresh());

        $this->assertSame(
            [
                Extension::ATTACHMENTS,
                Extension::BANDING_MORPHOMETRICS,
                Extension::OIL_SPILL_PROCESSING
            ],
            $result->pluck('extension')->sortBy('value')->values()->toArray()
        );
    }

    #[Test]
    public function itDeactivatesAnExtension(): void
    {
        $team = $this->activateExtension(Extension::ATTACHMENTS);

        ExtensionManager::deactivate($team, Extension::ATTACHMENTS);

        $result = ExtensionManager::getActivated($team->fresh());

        $this->assertCount(0, $result);
    }

    #[Test]
    public function dependentExtensionsAreDeactivatedWhenAParentIsDeactivated(): void
    {
        $team = Team::factory()->create();
        ExtensionManager::activate($team, Extension::OIL_SPILL_PROCESSING);

        ExtensionManager::deactivate($team->fresh(), Extension::OIL_SPILL_PROCESSING);

        $result = ExtensionManager::getActivated($team);

        $this->assertCount(0, $result);
    }

    #[Test]
    public function aDependentExtensionCanNotBeDeactivatedWhenItsParentIsActivated(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Can not deactivate extension because of active parent extension: '.Extension::OIL_SPILL_PROCESSING->label());

        $team = Team::factory()->create();
        ExtensionManager::activate($team, Extension::OIL_SPILL_PROCESSING);

        ExtensionManager::deactivate($team->fresh(), Extension::ATTACHMENTS);
    }

    #[Test]
    public function itDeterminesIfTheExtensionIsActivated(): void
    {
        $team = Team::factory()->create();

        $this->assertFalse(ExtensionManager::isActivated(Extension::ATTACHMENTS, $team));
        Cache::clear();

        $activated = $this->activateExtension(Extension::ATTACHMENTS, $team);

        $this->assertTrue(ExtensionManager::isActivated(Extension::ATTACHMENTS, $team->fresh()));
    }
}
