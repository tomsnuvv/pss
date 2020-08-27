<?php

namespace App\Nova\Filters\Installations;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use App\Models\Installation;

class SourceType extends Filter
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
        return $query->where('source_type', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        $options = [];
        $results = Installation::groupBy('source_type')->pluck('source_type');
        foreach ($results as $type) {
            $options[str_replace('App\\Models\\', '', $type)] = $type;
        }

        return $options;
    }
}
