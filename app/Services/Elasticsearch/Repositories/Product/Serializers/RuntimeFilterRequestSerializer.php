<?php

namespace App\Services\Elasticsearch\Repositories\Product\Serializers;

use Carbon\Carbon;
use App\Models\FieldType\FieldTypeName;
use App\Models\ProductFilter\ProductFilter;
use App\Exceptions\InvalidDataTypeException;
use App\Services\Elasticsearch\Repositories\Product\ProductSearchRepository;

final class RuntimeFilterRequestSerializer
{
    public function __construct(private readonly ProductSearchRepository $repository)
    {
    }

    /**
     * @throws InvalidDataTypeException
     */
    public function serialize(ProductFilter $filter, array $query): array
    {
        if ($filter->field->hasType(FieldTypeName::Date)) {
            foreach ($query as $element) {
                try {
                    $filter->field->type->validateDataType($element);
                } catch (InvalidDataTypeException $exception) {
                    throw new InvalidDataTypeException(
                        sprintf(
                            'Invalid data type for %s filter. %s',
                            $filter->name,
                            $exception->getMessage()
                        )
                    );
                }
            }

            foreach ($query as $key => $element) {
                $query[$key] = Carbon::parse($element);
            }
        }

        return [
            'terms' => [
                $this->repository->getSearchField($filter->field) => $query,
            ],
        ];
    }
}
