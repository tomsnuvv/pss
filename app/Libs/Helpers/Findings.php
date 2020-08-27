<?php

namespace App\Libs\Helpers;

use App\Models\Vulnerability;
use App\Models\VulnerabilityType;
use App\Models\Module;
use App\Models\Installation;
use Illuminate\Database\Eloquent\Model;

/**
 * Findings Helper class.
 */
class Findings
{
    /**
     * Create (if new) a Finding.
     *
     * @param  \App\Models\Module                  $module
     * @param  \Illuminate\Database\Eloquent\Model $target
     * @param  \Illuminate\Database\Eloquent\Model $childTarget
     * @param  \App\Models\Installation            $installation
     * @param  \App\Models\Vulnerability           $vulnerability
     * @param  \App\Models\VulnerabilityType       $vulnerabilityType
     * @param  string                              $details
     * @param  string                              $title
     * @param  string                              $uid (target vulnerability identifier)
     *
     * @todo What if a website (not an installation) contains multiple XSS vulnerabilities?
     * @throws \Exception
     * @return \App\Models\Finding|void
     */
    public static function createFinding(
        Module $module,
        Model $target,
        Model $childTarget = null,
        Installation $installation = null,
        Vulnerability $vulnerability = null,
        VulnerabilityType $vulnerabilityType = null,
        $details = null,
        $title = null,
        $uid = null
    ) {
        // Vulnerability Type is mandatory!
        if (!$vulnerabilityType) {
            if (!$vulnerability) {
                throw new \Exception("Vulnerability type missing");
            }
            $vulnerabilityType = $vulnerability->type;
        }

        $data = [
            'module_id' => $module->id,
            'installation_id' => $installation ? $installation->id : null,
        ];

        if ($childTarget) {
            $data['child_target_type'] = get_class($childTarget);
            $data['child_target_id'] = $childTarget->id;
        }

        if ($vulnerability) {
            $data['vulnerability_id'] = $vulnerability->id;
            $data['vulnerability_type_id'] = $vulnerabilityType ? $vulnerabilityType->id : $vulnerability->type->id;
        } elseif ($vulnerabilityType) {
            $data['vulnerability_type_id'] = $vulnerabilityType->id;
        } else {
            return;
        }

        if ($uid) {
            $data['uid'] = $uid;
        }

        $finding = $target->findings()->firstOrNew($data);

        if ($title) {
            $finding->title = $title;
        } else {
            if ($vulnerability) {
                $finding->title = $vulnerability->title;
            } elseif ($vulnerabilityType) {
                $finding->title = $vulnerabilityType->name;
            }
        }

        $finding->details = $details ?: $vulnerabilityType->details;

        // By default, the severity will be the same as the vulnerability type.
        // However this can be changed later by users.
        if ($vulnerabilityType->severity) {
            $finding->severity()->associate($vulnerabilityType->severity);
        }

        if ($vulnerability) {
            $finding->title = $vulnerability->title;
            $finding->vulnerability()->associate($vulnerability);
        }

        if (!$finding->isFalsePositive()) {
            // Make sure is open
            $finding->markAsOpen();
        }

        $finding->save();

        return $finding;
    }
}
