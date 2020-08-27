<?php

namespace App\Nova\Lenses\Products;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Laravel\Nova\Fields\ID;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\LensRequest;
use Laravel\Nova\Lenses\Lens;

class MostInstallations extends Lens
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
                ->leftJoin('product_types', 'product_types.id', '=', 'products.type_id')
                ->leftJoin('vendors', 'vendors.id', '=', 'products.vendor_id')
                ->leftJoin('product_licenses', 'product_licenses.id', '=', 'products.license_id')
                ->leftJoin('installations', 'installations.product_id', '=', 'products.id')
                ->groupBy([
                    'products.id',
                    'products.code',
                    'products.name',
                    'type',
                    'vendor',
                    'license',
                ])
                ->withCount('installations')
                ->orderBy('installations_count', 'desc')
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
            'products.id',
            'products.code',
            'products.name',
            DB::raw('product_types.name as type'),
            DB::raw('vendors.name as vendor'),
            DB::raw('product_licenses.name as license'),
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
            Text::make('License', 'license'),
            Text::make('Vendor', 'vendor'),
            Text::make('Code', 'code'),
            Text::make('Name', 'name'),
            Text::make('# Installations', 'installations_count'),
        ];
    }

    /**
     * Get the filters available for the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request)
    {
        return [
            new \App\Nova\Filters\Installations\SourceType
        ];
    }

    /**
     * Get the cards available for the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function cards(Request $request)
    {
        return [];
    }

    /**
     * Get the actions available on the lens.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request)
    {
        return parent::actions($request);
    }

    /**
     * Get the URI key for the lens.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'most-installations';
    }
}
