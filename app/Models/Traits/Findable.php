<?php

namespace App\Models\Traits;

use App\Models\Severity;

/**
 * Findable trait class for models.
 */
trait Findable
{
    /**
     * Max severity level of model findings.
     *
     * @return mixed
     */
    public function getMaxSeverity()
    {
        $query = $this->findings()->open();

        if (!$query->count()) {
            return 'Safe';
        }

        $severityId = $query->max('severity_id');
        $maxSeverity = Severity::find($severityId);

        return $maxSeverity ? $maxSeverity->name : 'Unknown';
    }
}
