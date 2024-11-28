<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

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
            Relation::getMorphAlias(get_class($revisionable)),
            $actual->subject_type
        );

        $this->assertEquals(
            $revisionable->id,
            $actual->subject_id
        );
    }
}
