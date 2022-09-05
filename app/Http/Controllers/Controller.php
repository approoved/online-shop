<?php

namespace App\Http\Controllers;

use http\Exception\RuntimeException;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

abstract class Controller extends BaseController
{
    use DispatchesJobs;
    use ValidatesRequests;
    use AuthorizesRequests;

    protected const MAX_PER_PAGE = 50;

    protected function transform(
        LengthAwarePaginator|Collection|Model $transformable,
        string $appends = null
    ): array {
        $model = is_subclass_of($transformable, Model::class)
            ? $transformable
            : $transformable->get(0);

        return fractal($transformable)
            ->transformWith($this->getModelTransformer($model, $appends))
            ->withResourceName(
                is_subclass_of($transformable, Model::class)
                ? null
                : 'data'
            )
            ->toArray();

    }

    private function getModelTransformer(
        Model $model,
        string $appends = null
    ): TransformerAbstract {
        $config = config('transformer');

        $modelClass = get_class($model);

        if (! array_key_exists($modelClass, $config)) {
            throw new RuntimeException(
                'Transformer class is not configured for %s model.',
                $modelClass
            );
        }

        return new $config[$modelClass]($appends);
    }

    public static function getMaxPerPage(): string
    {
        return static::MAX_PER_PAGE;
    }
}
