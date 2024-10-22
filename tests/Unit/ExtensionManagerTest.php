<?php

namespace Tests\Unit\Extensions;

use App\Domain\Accounts\Account;
use App\Domain\Users\User;
use App\Extensions\Extension;
use App\Extensions\ExtensionManager;
use DirectoryIterator;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class ExtensionManagerTest extends TestCase
{
    protected $extensionManager;

    protected function setUp(): void
    {
        parent::setUp();

        $this->extensionManager = new ExtensionManager();
    }

    #[Test]
    public function itGetsAllTheExtensions(): void
    {
        Extension::factory()->create(['name' => 'Lab']);
        Extension::factory()->create(['name' => 'Prescriptions']);

        cache()->forget('allExtensions');

        $extensions = $this->extensionManager->getAll()->pluck('name')->all();

        $this->assertContains('Lab', $extensions);
        $this->assertContains('Prescriptions', $extensions);
    }

    #[Test]
    public function itGetsTheActivatedExtensions(): void
    {
        $activated = $this->activateExtension('Foo');

        $result = $this->extensionManager->getActivated($activated['account']);

        $this->assertCount(1, $result);
    }

    #[Test]
    public function itActivatesAnExtension(): void
    {
        $activated = $this->activateExtension('Foo', null, ['name' => 'Foo']);

        $result = $this->extensionManager->getActivated($activated['account']);

        $this->assertEquals('Foo', $result[0]->name);
    }

    #[Test]
    public function activatedExtensionsCanNotBeActivatedMultipleTimes(): void
    {
        $account = Account::factory()->create();
        $extension = Extension::factory()->create();

        $this->assertCount(0, $this->extensionManager->getActivated($account));

        $this->extensionManager->activate($account, $extension->id);
        $this->assertCount(1, $this->extensionManager->getActivated($account));

        $this->extensionManager->activate($account, $extension->id);
        $this->assertCount(1, $this->extensionManager->getActivated($account));
    }

    #[Test]
    public function dependentExtensionsAreActivatedWhenAnExtensionIsActivated(): void
    {
        $dependentA = Extension::factory()->create(['name' => 'Foo']);
        $dependentB = Extension::factory()->create(['name' => 'Zip']);
        $dependentC = Extension::factory()->create(['name' => 'Bar', 'dependents' => [$dependentA->id, $dependentB->id]]);
        $activated = $this->activateExtension('Zap', null, ['name' => 'Zap', 'dependents' => [$dependentC->id]]);

        $result = $this->extensionManager->getActivated($activated['account']);

        $this->assertEquals('Bar', $result[0]->name);
        $this->assertEquals('Foo', $result[1]->name);
        $this->assertEquals('Zap', $result[2]->name);
        $this->assertEquals('Zip', $result[3]->name);
    }

    #[Test]
    public function itDeactivatesAnExtension(): void
    {
        $activated = $this->activateExtension('Bar');

        $this->extensionManager->deactivate($activated['account'], $activated['extension']->id);

        $result = $this->extensionManager->getActivated($activated['account']);

        $this->assertCount(0, $result);
    }

    #[Test]
    public function dependentExtensionsAreDeactivatedWhenAParentIsDeactivated(): void
    {
        $dependent = Extension::factory()->create(['name' => 'Foo']);
        $activated = $this->activateExtension('Bar', null, ['dependents' => [$dependent->id], 'name' => 'BarZap']);

        $this->extensionManager->deactivate($activated['account'], $activated['extension']->id);

        $result = $this->extensionManager->getActivated($activated['account']);

        $this->assertCount(0, $result);
    }

    #[Test]
    public function aDependentExtensionCanNotBeDeactivatedWhenItsParentIsActivated(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Can not deactivate extension because of active parent: BarZap');

        $dependent = Extension::factory()->create(['name' => 'Foo']);
        $activated = $this->activateExtension('Bar', null, ['dependents' => [$dependent->id], 'name' => 'BarZap']);

        $this->extensionManager->deactivate($activated['account'], $dependent->id);
    }

    #[Test]
    public function itDeterminesIfTheExtensionIsActivated(): void
    {
        $account = Account::factory()->create();

        $this->assertFalse($this->extensionManager->isActivated('FooBar', $account));
        Cache::clear();

        $activated = $this->activateExtension('FooBar', $account);

        $this->assertTrue($this->extensionManager->isActivated('FooBar', $account));
    }
}
