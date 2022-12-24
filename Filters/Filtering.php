<?php

namespace App\Filters;

use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * This Trait adds filter scope to the model
 * Note: if your model is "extends QMBaseModel" then it's already registered on QMBaseModel.
 * ****
 * The first step is to enable the filters on the model
 * protected $whitelistFilters = ['fooFilter','booFilter'];
 * ****
 * Example usage:
 * SomeModel::filter('SomeFilter')->get();
 * SomeModel::filter(['SomeFilter'])->get();
 * SomeModel::filter('SomeFooFilter', 'SomeBooFilter')->get();
 * SomeModel::filter(['SomeFooFilter', 'SomeBooFilter'])->get();
 * With single param:
 * SomeModel::filter('SomeFilter:Foo')->get();
 * SomeModel::filter(['SomeFilter:Foo'])->get();
 * With multiple param:
 * SomeModel::filter('SomeFilter:Foo,Boo')->get();
 * SomeModel::filter(['SomeFilter:Foo,Boo'])->get();
 * ****
 * Create Filter:
 * ---
 * With no params:
 * namespace App\Filters;
 * class SomeFilter
 * {
 *      public function handle($query, $next)
 *      {
 *          $query->whereNull('column');
 *          return $next($query);
 *      }
 * }
 * ----
 * With single param:
 * namespace App\Filters;
 * class SomeFilter
 * {
 *      public function handle($query, $next, string $value)
 *      {
 *          $query->where('column', $value);
 *          return $next($query);
 *      }
 * }
 * ----
 * With multiple params:
 * namespace App\Filters;
 * class SomeFilter
 * {
 *      public function handle($query, $next, ...$args)
 *      {
 *          $query->where('column', $args[0])->where('field', $args[0]);
 *          return $next($query);
 *      }
 * }
 */
trait Filtering
{
     /**
     * @param object $query
     * @param array $through
     * @return object
     */
    public function scopeFilter(object $query, ...$through): object
    {
        $this->modelAssignable($through);

        $through = $this->prefixPath($through);

        return app(Pipeline::class)
            ->send($query)
            ->through($through)
            ->thenReturn();
    }

    private function prefixPath($through): array
    {
        $through = is_array($through[0]) ? $through[0] : $through;
        return array_map(fn($value) => 'App\Filters\\' . $value, $through);
    }

    private function getWhitelistFilters(): array
    {
        return isset($this->whitelistFilters) ? (array) $this->whitelistFilters : [];
    }

    private function modelAssignable(array $through): void
    {
        $blocked = [];
        $whitelistFilters = $this->getWhitelistFilters();
        $totalWhitelistFilters = count($whitelistFilters);
        if ($totalWhitelistFilters) {
            foreach ($through as $filter) {
                $filter = Str::before($filter, ':');
                if (!in_array($filter, $whitelistFilters)) {
                    $blocked[] = $filter;
                }
            }
            $this->confirmModelAssignable($blocked);
        } else {
            $this->confirmModelAssignable($through);
        }
    }

    private function confirmModelAssignable(array $blocked): void
    {
        $errors = count($blocked);
        if ($errors) {
            throw new RuntimeException(
                trans_choice(
                    'The following filter is not set on the :model model $whitelistFilters: :filters' .
                    '|' .
                    'The following filters are not set on the :model model $whitelistFilters: :filters',
                    $errors,
                    [
                        'filters' => implode(', ', $blocked),
                        'model' => get_class($this)
                        ]
                )
            );
        }
    }
}
