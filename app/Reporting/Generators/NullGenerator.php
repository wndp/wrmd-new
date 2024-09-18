<?php

namespace App\Reporting\Generators;

use App\Reporting\Contracts\Generator;

class NullGenerator extends Generator
{
    /**
     * Generate the report.
     */
    public function handle(): void
    {
    }
}
