<?php

namespace App\Nova\Metrics;

use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;
use App\Models\Finding;
use DB;
use App\Models\Severity;
use App\Models\VulnerabilityType;

class FindingsBySeverity extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        $severities = [];
        foreach (Severity::all() as $severity) {
            $severities[$severity->id] = $severity->name;
        }

        $results = Finding::open()->select('severity_id', DB::raw('count(*) as total'))
                 ->groupBy('severity_id')
                 ->orderBy('total', 'DESC')
                 ->get();

        $data = [];
        foreach ($results as $result) {
            if (isset($severities[$result->severity_id])) {
                $data[$severities[$result->severity_id]] = $result->total;
            } else {
                $data['Unknown'] = $result->total;
            }
        }

        return $this->result($data)->colors([
            'Critical' => '#f00',
            'High'     => 'rgb(245, 87, 59)',
            'Medium'   => 'rgb(249, 144, 55)',
            'Low'      => 'rgb(242, 203, 34)',
            'Info'     => '#87ceeb',
            'Unknown'  => '#a2a2a2',
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
        return 'findings-by-severity';
    }
}
