<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use App\Models\FieldType\FieldTypeName;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\ProductField\ProductField;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Exceptions\InvalidAppConfigurationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use App\Services\Elasticsearch\Repositories\Product\ProductSearchRepository;

class UpdateProductsMapping implements ShouldQueue
{
    use Queueable;
    use Dispatchable;
    use SerializesModels;
    use InteractsWithQueue;

    public function __construct(private readonly ProductField $field)
    {
    }

    /**
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     * @throws InvalidAppConfigurationException
     */
    public function handle(ProductSearchRepository $repository): void
    {
        $mapping = [
            'field' => $this->field->getField(),
            'type' => FieldTypeName::get($this->field->type->name)
        ];

        $repository->putMappings([$mapping]);
    }
}
