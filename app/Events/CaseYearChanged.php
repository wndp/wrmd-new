<?php

namespace App\Events;

class CaseYearChanged
{
    public $event;

    public function __construct($event)
    {
        $this->event = $event;
    }
}
