<?php

namespace App\Http\Transformers;

use Exception;
use League\Fractal\Scope;
use http\Exception\RuntimeException;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\ResourceInterface;

abstract class BaseTransformer extends TransformerAbstract
{
    protected array $availableIncludes = [];

    protected array $appends = [];

    public function __construct(string|null $appends = null)
    {
        $config = config('transformer');

        $modelClass = array_search(static::class, $config);

        if ($modelClass === false) {
            throw new RuntimeException(
                'Model is not configured for transformer. Class - %s.',
                static::class
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
     * @param Scope $scope
     * @param string $includeName
     * @param mixed $data
     * @return bool|ResourceInterface
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

        $config = config('transformer');

        $includeClass = is_subclass_of($data->$includeName, Model::class)
            ? get_class($data->$includeName)
            : get_class($data->$includeName->get(0));

        if (! array_key_exists($includeClass, $config)) {
            throw new RuntimeException(
                'Transformer class is not configured for model. Class - %s.',
                $includeClass
            );
        }

        $transformerClass = $config[$includeClass];

        return is_subclass_of($data->$includeName, Model::class)
            ? $this->item($data->$includeName, new $transformerClass)
            : $this->collection($data->$includeName, new $transformerClass);
    }
}
