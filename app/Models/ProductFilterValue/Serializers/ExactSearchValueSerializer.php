<?php

namespace App\Models\ProductFilterValue\Serializers;

use Carbon\Carbon;
use App\Models\FieldType\FieldTypeName;
use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidDataTypeException;
use App\Exceptions\InvalidInputDataException;

final class ExactSearchValueSerializer
{
    /**
     * @throws InvalidInputDataException
     * @throws InvalidDataTypeException
     */
    public static function serialize(ProductFilter $filter, array $searchValue): array
    {
        if (! isset($searchValue['terms'])) {
            throw new InvalidInputDataException(
                'Field \'terms\' is required for Exact filter value.'
            );
        }

        $searchValue = array_intersect_key($searchValue, ['terms' => '']);

        foreach ($searchValue['terms'] as $key => $term) {
            try {
                $filter->field->type->validateDataType($term);
            } catch (InvalidDataTypeException $exception) {
                throw new InvalidDataTypeException(
                    sprintf(
                        'Invalid data type for search value \'terms\' field. %s',
                        $exception->getMessage()
                    )
                );
            }

            if ($filter->field->hasType(FieldTypeName::Date)) {
                $searchValue['terms'][$key] = Carbon::parse($term);
            }
        }

        return array_intersect_key($searchValue, ['terms' => '']);
    }
}
