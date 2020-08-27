<?php

namespace App\Nova\Metrics;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;
use App\Models\Finding;
use DB;
use App\Models\VulnerabilityType;

class FindingsByCategory extends Partition
{
    /**
     * The displayable name of the metric.
     *
     * @var string
     */
    public $name = 'Findings By Category (top 5)';

    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        $types = [];
        foreach (VulnerabilityType::all() as $type) {
            $types[$type->id] = $type->code;
        }

        $results = Finding::open()->select('vulnerability_type_id', DB::raw('count(*) as total'))
                 ->groupBy('vulnerability_type_id')
                 ->orderBy('total', 'DESC')
                 ->limit(5)
                 ->get();
        $data = [];

        foreach ($results as $result) {
            $data[$types[$result->vulnerability_type_id]] = $result->total;
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
        return now()->addMinutes(5);
    }

    /**
     * Get the URI key for the metric.
     *
     * @return string
     */
    public function uriKey()
    {
        return 'findings-by-category';
    }
}
