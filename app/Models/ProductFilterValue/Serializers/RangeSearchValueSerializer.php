<?php

namespace App\Models\ProductFilterValue\Serializers;

use Carbon\Carbon;
use App\Models\FieldType\FieldTypeName;
use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidDataTypeException;
use App\Exceptions\InvalidInputDataException;

class RangeSearchValueSerializer
{
    /**
     * @throws InvalidInputDataException
     * @throws InvalidDataTypeException
     */
    public static function serialize(ProductFilter $filter, array $searchValue): array
    {
        if (! isset($searchValue['from']) && ! isset($searchValue['to'])) {
            throw new InvalidInputDataException(
                'Field \'from\' or field \'to\' is required for Range filter value.'
            );
        }

        $searchValue = array_intersect_key($searchValue, ['from' => '', 'to' => '']);

        foreach ($searchValue as $key => $value) {
            try {
                $filter->field->type->validateDataType($value);
            } catch (InvalidDataTypeException $exception) {
                throw new InvalidDataTypeException(
                    sprintf(
                        'Invalid data type for search value \'%s\' field. %s',
                        $key,
                        $exception->getMessage()
                    )
                );
            }

            if ($filter->field->hasType(FieldTypeName::Date)) {
                $searchValue[$key] = Carbon::parse($value);
            }
        }

        return $searchValue;
    }
}
