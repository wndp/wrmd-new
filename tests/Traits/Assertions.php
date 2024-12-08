<?php

namespace Tests\Traits;

use App\Exceptions\RecordNotOwned;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Testing\TestResponse;
use PHPUnit\Framework\Assert;
use PHPUnit\Framework\Attributes\Before;

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

    #[Before]
    public function helperMacros()
    {
        /**
         * Assert that the response has an ownership validation error.
         */
        TestResponse::macro('assertOwnershipValidationError', function (string|null $message = null) {
            $message = $message ?: RecordNotOwned::message();

            if ($this->session()->has('notification.text')) {
                $this->assertStatus(302);
                Assert::assertSame($message, $this->session()->get('notification.text'));

                return $this;
            }

            $this->assertStatus(422)->assertExactJson([$message]);
        });
    }
}
