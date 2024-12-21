<?php

namespace App\Repositories;

use App\Actions\CustomFieldBuilder;
use App\Models\CustomField;
use App\Models\CustomValue;
use App\Models\Team;
use Illuminate\Support\Facades\Cache;

class CustomFieldsRepository
{
    /**
     * Save a custom field.
     *
     * @return \App\Extensions\CustomFields\CustomField
     */
    public static function save(array $data, Team $team)
    {
        return tap(new CustomField($data), function ($customField) use ($team) {
            $customField->team_field_id = static::determineNextAvailableAccountFieldId($team->id);
            $customField->team()->associate($team);
            $customField->save();
        });
    }

    /**
     * Get an accounts custom fields.
     *
     * @param  int  $teamId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getCustomFields($teamId)
    {
        return Cache::remember(
            "customFields.$teamId",
            30,
            fn () => CustomField::where('team_id', $teamId)->orderBy('team_field_id')->get()
        );
    }

    /**
     * Determine the next available account field ID.
     *
     * @param  int  $teamId
     * @return int
     */
    public static function determineNextAvailableAccountFieldId($teamId)
    {
        $usedAccountFieldIds = static::getCustomFields($teamId)->pluck('team_field_id')->all();

        $missing = array_diff(range(1, CustomFieldBuilder::NUMBER_OF_ALLOWED_FIELDS), $usedAccountFieldIds);

        return min($missing);
    }

    /**
     * Delete all the values belonging to a custom field.
     *
     * @return void
     */
    public static function flushCustomValues(CustomField $customField)
    {
        if ($customField->team_field_id <= CustomFieldBuilder::NUMBER_OF_ALLOWED_FIELDS) {
            CustomValue::where([
                'team_id' => $customField->team_id,
            ])
                ->update([
                    "custom_field_{$customField->team_field_id}" => null,
                ]);
        }
    }
}
