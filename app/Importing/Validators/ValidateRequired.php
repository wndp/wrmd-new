<?php

namespace App\Importing\Validators;

use App\Domain\Importing\Contracts\Validator;
use Illuminate\Support\Collection;

class ValidateRequired extends Validator
{
    /**
     * Validate the all records have a value for the provided WRMD column.
     */
    public function validate(Collection $collection, string $wrmdColumn): bool
    {
        try {
            $mappedColumn = session('import.mappedHeadings')[$wrmdColumn];
        } catch (ErrorException $e) {
            return false;
        }

        $result = $collection->pluck($mappedColumn)->filter(function ($value, $i) {
            if (empty($value)) {
                $this->failed->push([
                    'row' => $i + 2, // 2 because $i is 0 based and the first row in the spreadsheet is the header
                    'value' => $value,
                ]);

                return true;
            }

            return false;
        });

        return $result->count() === 0;

        return [
            'success' => $result->count() === 0,
            'data' => $this->failed,
        ];
    }
}
