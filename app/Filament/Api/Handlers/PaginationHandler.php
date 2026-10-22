<?php

declare(strict_types=1);

namespace App\Filament\Api\Handlers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * GET /api/{panel}/{slug}
 *
 * Query string:
 *   ?filter[status]=pending,confirmed   exact match (comma = any of)
 *   ?filter[search]=text                LIKE search over $searchable
 *   ?filter[booking_date_from]=Y-m-d    / _to, for every column in $dateFilters
 *   ?filter[trashed]=with|only          soft-deleted models only
 *   ?sort=-created_at                   any of $allowedSorts, `-` = desc
 *   ?include=hall,user                  any of $allowedIncludes
 *   ?per_page=15                        1–100
 */
abstract class PaginationHandler extends Handler
{
    public static ?string $uri = '/';

    protected static array $allowedFilters = [];

    protected static array $allowedSorts = ['id', 'created_at'];

    /** Columns for `filter[search]`; use `name->en` for translatable JSON columns. */
    protected static array $searchable = [];

    /** Date columns that get `{column}_from` / `{column}_to` filters. */
    protected static array $dateFilters = [];

    protected static string $defaultSort = '-id';

    public function handler(Request $request)
    {
        $perPage = min(max((int) $request->query('per_page', 15), 1), 100);

        $paginator = QueryBuilder::for(static::getEloquentQuery(), $request)
            ->allowedFilters(static::filterDefinitions())
            ->allowedSorts(static::$allowedSorts)
            ->allowedIncludes(static::$allowedIncludes)
            ->defaultSort(static::$defaultSort)
            ->paginate($perPage)
            ->appends($request->query());

        return static::$transformer::collection($paginator);
    }

    protected static function filterDefinitions(): array
    {
        $filters = array_map(fn (string $column) => AllowedFilter::exact($column), static::$allowedFilters);

        if (static::$searchable !== []) {
            $filters[] = AllowedFilter::callback('search', function (Builder $query, mixed $value): void {
                $term = '%' . (is_array($value) ? implode(',', $value) : $value) . '%';

                $query->where(function (Builder $query) use ($term): void {
                    foreach (static::$searchable as $column) {
                        $query->orWhere($column, 'like', $term);
                    }
                });
            });
        }

        foreach (static::$dateFilters as $column) {
            $filters[] = AllowedFilter::callback("{$column}_from", fn (Builder $query, $value) => $query->whereDate($column, '>=', $value));
            $filters[] = AllowedFilter::callback("{$column}_to", fn (Builder $query, $value) => $query->whereDate($column, '<=', $value));
        }

        if (in_array(SoftDeletes::class, class_uses_recursive(static::getModel()), true)) {
            // Applied in Handler::applyTrashedFilter(); declared here so it is an allowed filter.
            $filters[] = AllowedFilter::callback('trashed', fn () => null);
        }

        return $filters;
    }
}
