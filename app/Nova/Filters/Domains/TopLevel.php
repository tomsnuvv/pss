<?php

namespace App\Nova\Filters\Domains;

use Illuminate\Http\Request;
use Laravel\Nova\Filters\Filter;

class TopLevel extends Filter
{
    /**
     * The displayable name of the filter.
     *
     * @var string
     */
    public $name = 'Top Level';

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
        if ($value == 'top') {
            return $query->whereNull('domains.domain_id');
        } elseif ($value == 'sub') {
            return $query->whereNotNull('domains.domain_id');
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
        return [
            'Domains' => 'top',
            'Subdomains' => 'sub',
        ];
    }
}
