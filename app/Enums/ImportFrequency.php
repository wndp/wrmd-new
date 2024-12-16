<?php

namespace App\Enums;

enum ImportFrequency: int
{
    case RECORDS_PER_CHUNK = 250;
    case BATCHES_PER_CHUNK = 50;
}
