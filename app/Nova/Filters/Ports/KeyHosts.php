<?php

namespace App\Nova\Filters\Ports;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class KeyHosts extends Filter
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
        return $query->whereHas('host', function ($query) use ($value) {
            $query->where('key', $value);
        });
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
