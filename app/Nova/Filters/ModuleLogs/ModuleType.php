<?php

namespace App\Nova\Filters\ModuleLogs;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;
use App\Models\Module as Model;

class ModuleType extends Filter
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
        return $query->where('module_id', $value);
    }

    /**
     * Get the filter's available options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function options(Request $request)
    {
        return Model::orderBy('code', 'ASC')->pluck('id', 'code');
    }
}
