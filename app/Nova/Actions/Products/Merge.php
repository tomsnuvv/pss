<?php

namespace App\Nova\Actions\Products;

use App\Models\Product;
use Laravel\Nova\Fields\Number;
use Laravel\Nova\Actions\Action;
use Illuminate\Support\Collection;
use Laravel\Nova\Fields\ActionFields;
use Illuminate\Queue\SerializesModels;
use App\Helpers\Products;

class Merge extends Action
{
    use SerializesModels;

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return mixed
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        $original = Product::find($fields->product_id);

        foreach ($models as $product) {
            Products::merge($original, $product);
        }

        return Action::message('Products merged');
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Number::make('Model ID', 'product_id')->help(
                'The model ID that will be conserved.'
            ),
        ];
    }
}
