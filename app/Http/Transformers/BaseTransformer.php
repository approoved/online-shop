<?php

namespace App\Http\Transformers;

use Exception;
use RuntimeException;
use League\Fractal\Scope;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;
use Illuminate\Database\Eloquent\Collection;
use League\Fractal\Resource\ResourceInterface;

abstract class BaseTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [];

    protected array $appends = [];

    abstract public function transform(Model $model): array;

    public function __construct(string|null $appends = null)
    {
        $config = config('transformer');

        $modelClass = array_search(static::class, $config);

        if ($modelClass === false) {
            throw new RuntimeException(
                sprintf(
                    'Model is not configured for transformer. Class - %s.',
                    static::class
                )
            );
        }

        $availableIncludes = [];

        foreach (get_class_vars($modelClass)['allowedIncludes'] as $include) {
            if (! in_array(strtok($include, '.'), $availableIncludes)) {
                $availableIncludes[] = strtok($include, '.');
            }
        }

        $this->availableIncludes = $availableIncludes;
        $this->appends = explode(',', $appends) ?? [];
    }

    /**
     * @throws Exception
     */
    protected function callIncludeMethod(Scope $scope, string $includeName, $data): bool|ResourceInterface
    {
        $methodName = 'include' . str_replace(
            ' ',
            '',
            ucwords(str_replace(
                '_',
                ' ',
                str_replace(
                    '-',
                    ' ',
                    $includeName
                )
            ))
        );

        if (method_exists(static::class, $methodName)) {
            return parent::callIncludeMethod($scope, $includeName, $data);
        }

        $model = $data;
        $include = $model->$includeName;
        $includeObject = $include instanceof Collection
            ? $include->first()
            : $include;

        $transformer = $this->getIncludeObjectTransformer($includeObject);

        return $model->$includeName instanceof Collection
            ? $this->collection($data->$includeName, $transformer)
            : $this->item($data->$includeName, $transformer);
    }

    private function getIncludeObjectTransformer(Model|null $includeObject): TransformerAbstract
    {
        if ($includeObject === null) {
            return new NullTransformer();
        }

        $config = config('transformer');

        $includeClass = get_class($includeObject);

        if (! array_key_exists($includeClass, $config)) {
            throw new RuntimeException(
                sprintf(
                    'Transformer class is not configured for model. Class - %s.',
                    $includeClass
                )
            );
        }

        return new $config[$includeClass]();
    }
}
