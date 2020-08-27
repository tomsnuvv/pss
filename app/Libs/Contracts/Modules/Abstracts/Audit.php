<?php

namespace App\Libs\Contracts\Modules\Abstracts;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Models\Installation;
use App\Models\VulnerabilityType;
use App\Models\Vulnerability;
use App\Libs\Helpers\Findings;
use Illuminate\Database\Eloquent\Model;

/**
 * Audit Module abstract.
 */
abstract class Audit extends Module
{
    /**
     * Vulnerability Type Code.
     *
     * @var string
     */
    protected $vulnerabilityTypeCode;

    /**
     * Get the associated vulnerability type.
     *
     * @return \App\Models\VulnerabilityType|void
     */
    protected function getVulnerabilityType()
    {
        if ($this->vulnerabilityTypeCode) {
            return VulnerabilityType::whereCode($this->vulnerabilityTypeCode)->first();
        }
    }

    /**
     * Perform the audit actions.
     */
    protected function audit()
    {
        if ($this->isVulnerable()) {
            $this->vulnerable();
        }
    }

    /**
     * Perform the actions for a vulnerable result.
     */
    protected function vulnerable()
    {
        $this->storeFinding();
    }

    /**
     * {@inheritdoc}
     */
    protected function finish()
    {
        $this->solvePreviousFindings();
    }

    /**
     * Create (if new) a Finding.
     *
     * Null parameters will be assumed based on the current module class properties.
     *
     * @param  \Illuminate\Database\Eloquent\Model $target
     * @param  \Illuminate\Database\Eloquent\Model $childModel
     * @param  \App\Models\Vulnerability           $vulnerability
     * @param  \App\Models\VulnerabilityType       $type
     * @param  string                              $details
     * @param  string                              $title
     * @param  string                              $uid (target vulnerability identifier)
     * @return \App\Model\Finding
     */
    protected function storeFinding(
        Model $target = null,
        Model $childModel = null,
        Vulnerability $vulnerability = null,
        VulnerabilityType $type = null,
        $details = null,
        $title = null,
        $uid = null
    )
    {
        $target = $target ?: $this->model;

        $installation = null;

        switch (get_class($target)) {
            case 'App\\Models\\Port':
                $childModel = $target;
                $target = $target->host;
                $installation = $target->installation ?: null;
            break;
            case 'App\\Models\\Installation':
                $installation = $target;
                $target = $installation->source;
                if (get_class($target) == 'App\\Models\\Port') {
                    $childModel = $target; // Port
                    $target = $target->host; // Host
                }
            break;
        }

        $type = $type ?: $this->getVulnerabilityType();
        if (!$type && $vulnerability) {
            $type = $vulnerability->type;
        }
        $details = $details ?: $this->getDetails();

        $finding = Findings::createFinding($this->getModuleModel(), $target, $childModel, $installation, $vulnerability, $type, $details, $title, $uid);
        $this->outputDetail('Finding', $finding->title);

        $this->items[] = $finding;

        return $finding;
    }

    /**
     * Mark the non-found findings as solved.
     */
    protected function solvePreviousFindings()
    {
        $module = $this->getModuleModel();
        $query = $this->model->findings()->where('module_id', $module->id)->open();

        if (!empty($this->items)) {
            $query->whereNotIn('id', array_column($this->items, 'id'));
        }

        foreach ($query->get() as $finding) {
            if ($finding) {
                $this->outputDetail('Fixed', $finding->title);
                $finding->markAsFixed();
            }
        }
    }

    /**
     * Perform the audit checks.
     *
     * Returns true if it's vulnerable.
     *
     * @return bool
     */
    protected function isVulnerable()
    {
        return false;
    }

    /**
     * Get finding details.
     */
    protected function getDetails()
    {
        // Does nothing
    }
}
