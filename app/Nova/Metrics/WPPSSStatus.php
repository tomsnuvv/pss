<?php

namespace App\Nova\Metrics;

use App\Models\Module;
use App\Models\ModuleLogStatus;
use App\Models\Website;
use Illuminate\Http\Request;
use Laravel\Nova\Metrics\Partition;

class WPPSSStatus extends Partition
{
    /**
     * The displayable name of the metric.
     *
     * @var string
     */
    public $name = 'WP Sync Status';

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

        // Define the array indexes
        foreach (ModuleLogStatus::all() as $status) {
            $data[$status->name] = 0;
        }

        foreach ($websites as $website) {
            // Get the latest Website WPPSS module log
            $log = $website->moduleLogs()
                ->where('module_id', $this->getModuleId())
                ->latest('id')->first();
            if (!isset($log->status)) {
                continue;
            }
            $data[$log->status->name] += 1;
        }

        // Remove empty indexes
        foreach ($data as $index => $value) {
            if ($value === 0) {
                unset($data[$index]);
            }
        }

        return $this->result($data)->colors([
            'Executed' => 'rgb(71, 193, 191)',
            'Finished' => 'rgb(143, 193, 93)',
            'Cant run' => 'rgb(242, 203, 34)',
            'Error' => 'rgb(245, 87, 59)',
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
        return 'wppss-status';
    }
}
