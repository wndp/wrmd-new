<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;

trait Assertions
{
    public function assertRevisionRecorded(Model $revisionable, string $expectedAction): void
    {
        $actual = $revisionable->activities->last();

        $this->assertEquals(
            $expectedAction,
            $actual->event
        );

        $this->assertEquals(
            get_class($revisionable),
            $actual->subject_type
        );

        $this->assertEquals(
            $revisionable->id,
            $actual->subject_id
        );
    }
}
