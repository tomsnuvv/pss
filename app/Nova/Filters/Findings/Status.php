<?php

namespace App\Nova\Filters\Findings;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use App\Models\FindingStatus;

class Status extends Filter
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
        return $query->where('status_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return FindingStatus::all()->pluck('id', 'name');
    }

    /**
     * The default value of the filter.
     *
     * @var string
     */
    public function default()
    {
        return FindingStatus::open()->first()->id;
    }
}
