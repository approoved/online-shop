<?php

namespace App\Services\Elasticsearch\Commands;

use App\Models\Product\Product;
use Illuminate\Console\Command;
use App\Models\FieldType\FieldTypeName;
use App\Models\ProductField\ProductField;
use App\Exceptions\InvalidAppConfigurationException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;
use Elastic\Elasticsearch\Exception\MissingParameterException;
use App\Services\Elasticsearch\Repositories\Product\ProductSearchRepository;

class ReindexProduct extends Command
{
    protected $signature = 'elasticsearch:reindex-product';

    protected $description = 'Recreates products index and indexes all products to Elasticsearch';

    public function __construct(private readonly ProductSearchRepository $repository)
    {
        parent::__construct();
    }

    /**
     * @throws InvalidAppConfigurationException
     * @throws ClientResponseException
     * @throws ServerResponseException
     * @throws MissingParameterException
     */
    public function handle(): void
    {
        $this->info('Reindexing all products. This might take a while...');

        if ($this->repository->checkIndexExists()) {
            $this->repository->deleteIndex();
        }

        $this->repository->createIndex();

        $fields = ProductField::query()->with(['group', 'type'])->get();
        $mappings = [];

        /** @var ProductField $field */
        foreach ($fields as $field) {
            $mappings[] = [
                'field' => $this->repository->getSearchField($field),
                'type' => FieldTypeName::get($field->type->name),
            ];
        }

        $this->repository->putMappings($mappings);

        foreach (Product::query()->cursor() as $product) {
            $this->repository->store($product);

            $this->output->write('.');
        }

        $this->info('Products reindexed successfully');
    }
}
