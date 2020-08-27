<?php

namespace App\Nova\Metrics;

use App\Models\Module;
use App\Models\ModuleLogStatus;
use App\Models\Website;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;

class WPPSSIssues extends Partition
{
    /**
     * The displayable name of the metric.
     *
     * @var string
     */
    public $name = 'WP Sync Issues';

    /**
     * Module Code.
     *
     * @var string
     */
    const MODULE = 'Discovery\Websites\Products\WordPress\WPPSS';

    /**
     * Calculate the value of the metric.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function calculate(Request $request)
    {
        $websites = Website::has('token')->get();

        $data = [];
        foreach ($websites as $website) {
            // Get the latest Website WPPSS module lo
            $log = $website->moduleLogs()
                ->where('module_id', $this->getModuleId())
                ->latest('id')->first();
            if (!$log) {
                continue;
            }
            // Erroneous statuses only
            if ($log->status_id != $this->getModuleStatusId()) {
                continue;
            }

            // Count
            if (isset($data[$log->details])) {
                $data[$log->details] += 1;
            } else {
                $data[$log->details] = 1;
            }
        }
        $data = array_slice($data, 0, 5, true);

        return $this->result($data)->colors([
            'rgb(71, 193, 191)', 'rgb(228, 113, 222)', 'rgb(156, 106, 222)', 'rgb(100, 116, 215)', 'rgb(22, 147, 235)',
        ]);
    }

    /**
     * Get the WPPSS Module Id.
     *
     * @return int
     */
    private function getModuleId()
    {
        return Module::whereCode(self::MODULE)->first()->id;
    }

    /**
     * Get the Module Status Id.
     *
     * @return int
     */
    private function getModuleStatusId()
    {
        return ModuleLogStatus::error()->first()->id;
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
        return 'wppss-issues';
    }
}
