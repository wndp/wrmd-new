<?php

namespace Tests\Traits;

use App\Repositories\OptionsStore;
use Inertia\Testing\AssertableInertia;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Before;

trait FeatureMacros
{
    #[Before]
    public function helperMacros()
    {
        AssertableInertia::macro('hasOption', function ($option) {
            Assert::assertArrayHasKey($option, OptionsStore::all());

            return $this;
        });

        AssertableInertia::macro('hasOptions', function (array $options) {
            foreach ($options as $option) {
                $this->hasOption($option);
            }

            return $this;
        });
    }
}
