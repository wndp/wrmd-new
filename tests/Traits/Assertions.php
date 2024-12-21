<?php

namespace Tests\Traits;

use App\Enums\SettingKey;
use App\Exceptions\RecordNotOwned;
use App\Models\Setting;
use App\Models\Team;
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

    public function assertTeamHasSetting(Team $team, SettingKey $key, $value = null)
    {
        $setting = Setting::where([
            'team_id' => $team->id,
            'key' => $key->value,
        ])->first();

        $this->assertInstanceOf(
            Setting::class,
            $setting,
            "Unable to find setting with key [{$key->value}] for team [{$team->id}]."
        );

        if ($value) {
            $expected = json_encode($value);
            $actual = json_encode($setting->value);

            $this->assertEquals(
                $expected,
                $actual,
                "Expected setting with key [{$key->value}] to have value [{$expected}] but it had {$actual} instead."
            );
        }

        return $this;
    }

    public function assertTeamMissingSetting(Team $team, SettingKey $key): void
    {
        $count = Setting::where([
            'team_id' => $team->id,
            'key' => $key->value,
        ])->count();

        $this->assertSame(
            0,
            $count,
            "Found unexpected setting with key [{$key->value}] for team [{$team->id}]."
        );
    }

    #[Before]
    public function assertionMacros()
    {
        /**
         * Assert that the response has an ownership validation error.
         */
        TestResponse::macro('assertOwnershipValidationError', function (?string $message = null) {
            $message = $message ?: RecordNotOwned::message();

            if ($this->session()->has('notification.text')) {
                $this->assertStatus(302);
                Assert::assertSame($message, $this->session()->get('notification.text'));

                return $this;
            }

            $this->assertStatus(422)->assertExactJson([$message]);
        });

        TestResponse::macro('assertHasNotificationMessage', function ($message) {
            $this->assertSessionHas('notification.text', $message);
        });
    }
}
