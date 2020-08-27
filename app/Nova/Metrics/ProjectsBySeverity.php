<?php

namespace App\Nova\Metrics;

use App\Models\Project;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;

class ProjectsBySeverity extends Partition
{
    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        $data = [];
        foreach (Project::all() as $project) {
            $severity = $project->getMaxSeverity();
            if (isset($data[$severity])) {
                $data[$severity]++;
            } else {
                $data[$severity] = 0;
            }
        }

        return $this->result($data)->colors([
            'Critical' => '#f00',
            'High' => 'rgb(245, 87, 59)',
            'Medium' => 'rgb(249, 144, 55)',
            'Low' => 'rgb(242, 203, 34)',
            'Info' => '#87ceeb',
            'Unknown' => '#a2a2a2',
            'Safe' => 'rgb(143, 193, 93)',
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
        return 'projects-severity';
    }
}
