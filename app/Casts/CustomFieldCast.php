<?php

namespace App\Casts;

use App\Models\CustomField;
use App\Repositories\CustomFieldsRepository;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use InvalidArgumentException;

class CustomFieldCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function get(Model $model, string $key, mixed $value, array $attributes)
    {
        // if ($this->isDateLike($value)) {
        //     //return $this->createDateFromFormat($value);
        // } elseif ($this->isCheckBoxGroup($model, $key)) {
        //     return (array) json_decode($value);
        // }

        return json_decode($value);

        // $decoded = $value;

        // if (preg_match('/^{|\\[/u', $value)) {
        //     $decoded = json_decode($value, true);

        //     if (json_last_error() !== JSON_ERROR_NONE) {
        //         $decoded = $value;
        //     }
        // }

        // return $decoded;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes)
    {
        if (is_null($value)) {
            return null;
        } elseif ($this->isDateLike($value)) {
            //return $this->createDateFromFormat($value);
            // } elseif ($this->isCheckBoxGroup($model, $key) && empty($value)) {
            //     return null;
        }

        return json_encode($value);
    }

    /**
     * Determine of the given value is a date "like" string.
     *
     * @param  string  $value
     * @return bool
     */
    private function isDateLike($value)
    {
        return is_string($value) && preg_match('/^(\d{4})-(\d{1,2})-(\d{1,2})/', $value);
    }

    public function isCheckBoxGroup($model, $key)
    {
        $customField = CustomFieldsRepository::getCustomFields($model->team_id)
            ->firstWhere('account_field_id', Str::remove('custom_field_', $key));

        return $customField instanceof CustomField and $customField->type === 'checkbox-group';
    }

    /**
     * Create a date string from the format of the provided value.
     *
     * @param  string  $value
     * @return string
     */
    public function createDateFromFormat($value)
    {
        try {
            return Carbon::createFromFormat('!Y-m-d', $value)->format('Y-m-d');
        } catch (InvalidArgumentException $e) {
            try {
                return Carbon::createFromFormat('!Y-m-d H:i', $value)->format('Y-m-d H:i:s');
            } catch (InvalidArgumentException $e) {
                try {
                    return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('Y-m-d H:i:s');
                } catch (InvalidArgumentException $e) {
                    return $value;
                }
            }
        }
    }
}
