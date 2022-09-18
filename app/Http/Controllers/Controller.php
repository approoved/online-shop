<?php

namespace App\Http\Controllers;

use http\Exception\RuntimeException;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;
use App\Http\Transformers\NullTransformer;
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
        LengthAwarePaginator|Collection|Model|null $transformable,
        string $appends = null
    ): array {
        $isTransformableCollectionOrPaginator = $transformable instanceof Collection
            || $transformable instanceof LengthAwarePaginator;

        $model = $isTransformableCollectionOrPaginator
            ? $transformable->get(0)
            : $transformable;

        return fractal($transformable)
            ->transformWith($this->getModelTransformer($model, $appends))
            ->withResourceName(
                $isTransformableCollectionOrPaginator
                ? 'data'
                : null
            )
            ->toArray();
    }

    private function getModelTransformer(
        Model|null $model,
        string $appends = null
    ): TransformerAbstract {
        if ($model === null) {
            return new NullTransformer();
        }

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
