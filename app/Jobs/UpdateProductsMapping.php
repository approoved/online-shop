<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\ProductField\ProductField;
use App\Models\ProductField\FieldTypeName;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Services\Elasticsearch\Elasticsearch;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;

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
     */
    public function handle(): void
    {
        Elasticsearch::getInstance()->putMapping(
            'products',
            sprintf(
                'short_details.%s.%s',
                $this->field->group->name,
                $this->field->name
            ),
            FieldTypeName::get($this->field->type->name)
        );
    }
}
