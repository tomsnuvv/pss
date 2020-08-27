<?php

namespace App\Nova\Filters\Products;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class WithInstallations extends Filter
{
    /**
     * Apply the filter to the given query.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(Request $request, $query, $value)
    {
        if ($value == 1) {
            return $query->has('installations');
        } elseif ($value == 0) {
            return $query->doesntHave('installations');
        }
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return ['No' => 0, 'Yes' => 1];
    }
}
