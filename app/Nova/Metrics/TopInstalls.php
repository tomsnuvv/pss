<?php

namespace App\Nova\Metrics;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;
use App\Models\Installation;
use App\Models\Product;
use DB;

class TopInstalls extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        $results = Installation::select('product_id', DB::raw('count(*) as total'))
                 ->groupBy('product_id')
                 ->orderBy('total', 'DESC')
                 ->limit(5)
                 ->get();
        $data = [];
        foreach ($results as $result) {
            $product = Product::find($result->product_id);
            $data[$product->code] = $result->total;
        }

        return $this->result($data)->colors([
            'rgb(71, 193, 191)', 'rgb(228, 113, 222)', 'rgb(156, 106, 222)', 'rgb(100, 116, 215)', 'rgb(22, 147, 235)',
        ]);
    }

    /**
     * Determine for how many minutes the metric should be cached.
     *
     * @return  \DateTimeInterface|\DateInterval|float|int
     */
    public function cacheFor()
    {
        // return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'installs-top';
    }
}
