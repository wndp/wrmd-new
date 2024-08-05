<?php

namespace App\Importing;

class Declarations
{
    public $sessionId;

    public $mappedHeadings;

    public $translatedValues;

    public $spreadsheetExistingColumn;

    public $wrmdExistingColumn;

    public function __construct(array $declarations)
    {
        $this->sessionId = $declarations['sessionId'] ?? null;
        $this->mappedHeadings = (array) ($declarations['mappedHeadings'] ?? []);
        $this->translatedValues = (array) ($declarations['translatedValues'] ?? []);
        $this->spreadsheetExistingColumn = $declarations['spreadsheetExistingColumn'] ?? null;
        $this->wrmdExistingColumn = $declarations['wrmdExistingColumn'] ?? null;
    }
}
