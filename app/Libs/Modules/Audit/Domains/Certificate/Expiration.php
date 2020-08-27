<?php

namespace App\Libs\Modules\Audit\Domains\Certificate;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Models\VulnerabilityType;

/**
 * Domains Certificate Expiration Audit Module.
 *
 * Checks a domain certificate will exprie soon or is already expired.
 */
class Expiration extends Audit
{
    /**
     * Days left for the "Expires Soon" finding.
     *
     * @var int
     */
    const EXPIRES_SOON_DAYS = 30;

    /**
     * Days for the "Expires too late" finding.
     *
     * @var int
     */
    const EXPIRES_TOO_LATE_DAYS = 398;

    /**
     * Vulnerability Type Code for "Expired" Finding.
     *
     * @var string
     */
    const TYPE_EXPIRED = 'SSLDATEEXPIRED';

    /**
     * Vulnerability Type Code for "Expires Soon" Finding.
     *
     * @var string
     */
    const TYPE_EXPIRES_SOON_EXPIRE = 'SSLDATESOON';

    /**
     * Vulnerability Type Code for "Expires Soon" Finding.
     *
     * @var string
     */
    const TYPE_EXPIRES_TOO_LATE_EXPIRE = 'SSLDATETOOLONG';

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if (!$this->model->certificate) {
            $this->setMessage('Certificate not found');
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $days = $this->model->certificate->daysToExpire();
        $code = null;

        if ($days < 1) {
            $code = self::TYPE_EXPIRED;
        } elseif ($days < self::EXPIRES_SOON_DAYS) {
            $code = self::TYPE_EXPIRES_SOON_EXPIRE;
        } elseif ($days > self::EXPIRES_TOO_LATE_DAYS && $this->model->certificate->expiration_date <= '2020-09-01 00:00:00') {
            $code = self::TYPE_EXPIRES_TOO_LATE_EXPIRE;
        }

        if ($code) {
            $vulnerability = VulnerabilityType::whereCode($code)->first();
            $this->storeFinding($this->model, $this->model->certificate, null, $vulnerability);
        }
    }
}
