<?php

namespace App\Analytics\Contracts;

use App\Analytics\Concerns\HandleSeriesNames;

abstract class Map extends Analytic
{
    use HandleSeriesNames;
}
