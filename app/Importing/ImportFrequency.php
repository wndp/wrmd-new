<?php

namespace App\Importing;

class ImportFrequency
{
    /**
     * The number of records to import per chunk.
     */
    public const RECORDS_PER_CHUNK = 250;

    /**
     * The average number of chunks per hour.
     */
    public const BATCHES_PER_CHUNK = 50;
}
