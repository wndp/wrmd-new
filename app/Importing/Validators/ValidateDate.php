<?php

namespace App\Importing\Validators;

use App\Domain\Importing\Contracts\Validator;
use App\Domain\Importing\ValueTransformer;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ValidateDate extends Validator
{
    /**
     * Validate the all records have a null or date value for the provided WRMD column.
     */
    public function validate(Collection $collection, string $wrmdColumn): bool
    {
        try {
            $mappedColumn = session('import.mappedHeadings')[$wrmdColumn];
        } catch (ErrorException $e) {
            return false;
        }

        ValueTransformer::setDateAttributes();

        $result = $collection->pluck($mappedColumn)->filter(function ($value, $i) use ($wrmdColumn) {
            try {
                $transformedValue = ValueTransformer::handle($wrmdColumn, $value);

                if (is_null($transformedValue) || $transformedValue instanceof Carbon) {
                    return false;
                }
            } catch (\Exception $e) {
            }

            $this->failed->push([
                'row' => $i + 2, // 2 because $i is 0 based and the first row in the spreadsheet is the header
                'value' => $value,
            ]);

            return true;
        });

        return $result->count() === 0;
    }
}
