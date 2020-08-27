<?php

namespace App\Nova\Lenses\Vulnerabilities;

use Illuminate\Http\Request;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Date;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Lenses\Lens;
use Laravel\Nova\Http\Requests\LensRequest;
use Illuminate\Support\Facades\DB;

class Top extends Lens
{
    /**
     * Get the query builder / paginator for the lens.
     *
     * @param  \Laravel\Nova\Http\Requests\LensRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return mixed
     */
    public static function query(LensRequest $request, $query)
    {
        return $request->withOrdering($request->withFilters(
            $query->select(self::columns())
                ->leftJoin('vulnerability_types', 'vulnerability_types.id', '=', 'vulnerabilities.type_id')
                ->leftJoin('products', 'products.id', '=', 'vulnerabilities.product_id')
                ->groupBy([
                    'vulnerabilities.id',
                    'vulnerability_types.name',
                    'products.name',
                    'vulnerabilities.title',
                    'vulnerabilities.date',
                ])
                ->withCount('findings')
                ->orderBy('findings_count', 'desc')
        ));
    }

    /**
     * Get the columns that should be selected.
     *
     * @return array
     */
    protected static function columns()
    {
        return [
            'vulnerabilities.id',
            DB::raw('vulnerability_types.name as type'),
            'products.name',
            'vulnerabilities.title',
            'vulnerabilities.date',
        ];
    }

    /**
     * Get the fields available to the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request)
    {
        return [
            ID::make('ID', 'id'),
            Text::make('Type', 'type'),
            Text::make('Product', 'name'),
            Text::make('Title', 'title'),
            Date::make('Date', 'date'),
            Text::make('# Findings', 'findings_count'),
        ];
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'top-vulnerabilities';
    }
}
