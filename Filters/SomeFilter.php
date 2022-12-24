<?php

namespace App\Filters;

use Closure;

class SomeFilter
{
    public function handle(object $query, Closure $next): object
    {
        $query->whereNull('foo');
        return $next($query);
    }
}
