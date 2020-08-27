<?php

namespace App\Libs\Modules\Audit\Domains\Email;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Libs\Contracts\Modules\Traits\Process;
use App\Models\VulnerabilityType;

/**
 * Email DMARC Domains Audit Module.
 *
 * Audits SPF, DMARC & DKIM DNS records.
 * Uses checkdmarc tool:
 * https://github.com/domainaware/checkdmarc
 */
class DMARC extends Audit
{
    use Process;

    /**
     * Process result.
     *
     * @var object
     */
    protected $result;

    /**
     * Findings array.
     *
     * @var array
     */
    protected $findings = [
        'MX_ISSUES' => [],
        'SPF_ISSUES' => [],
        'DMARC_ISSUES' => [],
    ];

    /**
     * Findings to ignore.
     *
     * @var array
     */
    const IGNORE = [
        'No MX records found',
        'Connection timed out',
        'Connection refused',
        'Certificate error',
        'SSL error',
        'Could not connect',

    ];

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
            return false;
        }

        if ($this->model->parent()->exists()) {
            $this->setMessage('Not a top level domain');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $result = $this->runProcess(['checkdmarc', $this->model->name]);
        $this->result = json_decode($result);
        if (!is_object($this->result)) {
            throw new \Exception('Output JSON malformed');
        }

        $this->audit();
    }

    /**
     * {@inheritdoc}
     */
    protected function audit()
    {
        $this->auditMX();
        $this->auditSPF();
        $this->auditDMARC();

        foreach ($this->findings as $type => $findings) {
            foreach ($findings as $finding) {
                if ($this->isIgnored($finding)) {
                    continue;
                }
                $this->storeFinding(null, null, null, VulnerabilityType::whereCode($type)->first(), $finding);
            }
        }
    }

    /**
     * Check if the finding must be ignored.
     *
     * @param  string  $finding
     * @return boolean
     */
    protected function isIgnored($finding)
    {
        foreach (self::IGNORE as $ignored) {
            if (strstr($finding, $ignored)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Audits MX.
     */
    protected function auditMX()
    {
        if (!isset($this->result->mx)) {
            return;
        }
        if (isset($this->result->mx->warnings)) {
            foreach ($this->result->mx->warnings as $warning) {
                $this->findings['MX_ISSUES'][] = $warning;
            }
        }
        if (isset($this->result->mx->error)) {
            $this->findings['MX_ISSUES'][] = $this->result->mx->error;
        }
    }

    /**
     * Audits SPF.
     */
    protected function auditSPF()
    {
        if (!isset($this->result->spf)) {
            return;
        }
        if (isset($this->result->spf->warnings)) {
            foreach ($this->result->spf->warnings as $warning) {
                $this->findings['SPF_ISSUES'][] = $warning;
            }
        }
        if (isset($this->result->spf->error)) {
            $this->findings['SPF_ISSUES'][] = $this->result->spf->error;
        }

        if (strstr(strtolower($this->result->spf->record), '?all')) {
            $this->findings['SPF_ISSUES'][] = 'SPF record without a policy statement (neutral: ?all)';
        }
        if (strstr(strtolower($this->result->spf->record), '+all')) {
            $this->findings['SPF_ISSUES'][] = 'SPF record is allowing all mail (pass: +all)';
        }
    }

    /**
     * Audits DMARC.
     */
    protected function auditDMARC()
    {
        if (!isset($this->result->dmarc)) {
            return;
        }
        if (isset($this->result->dmarc->warnings)) {
            foreach ($this->result->dmarc->warnings as $warning) {
                $this->findings['DMARC_ISSUES'][] = $warning;
            }
        }
        if (isset($this->result->dmarc->error)) {
            $this->findings['DMARC_ISSUES'][] = $this->result->dmarc->error;
        }

        if (strstr(strtolower($this->result->dmarc->record), 'p=none')) {
            $this->findings['SPF_ISSUES'][] = 'DMARC record without a policy statement (p=none)';
        }
    }
}
