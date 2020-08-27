<?php

namespace App\Libs\Modules\Audit\Domains\Certificate;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Models\VulnerabilityType;
use Illuminate\Support\Facades\Storage;
use \App\Libs\Contracts\Modules\Traits\Process;

/**
 * Domains Certificate TestSSL Audit Module.
 *
 * Audits SSL Vulnerabilities (HEARTBLEED, ROBOT, BREACH...) by using TestSSL.sh.
 * https://github.com/drwetter/testssl.sh
 */
class TestSSL extends Audit
{
    use Process;

    /**
     * Vulnerability Types relationship.
     *
     * LUCKY13 and BREACH were removed due false positives.
     *
     * @var array
     */
    const TYPES_CODES = [
        // Vulnerabilities
        "heartbleed" => "SSL_HEARTBLEED",
        "CCS" => "SSL_CCS",
        "ticketbleed" => "SSL_TICKETBLEED",
        "ROBOT" => "SSL_ROBOT",
        "secure_renego" => "SSL_SECURE_RENEGO",
        "secure_client_renego" => "SSL_SECURE_RENEGO",
        "CRIME_TLS" => "SSL_CRIME_TLS",
        # "BREACH" => "SSL_BREACH",
        "POODLE_SSL" => "SSL_POODLE",
        "fallback_SCSV" => "SSL_FALLBACK_SCSV",
        "SWEET32" => "SSL_SWEET32",
        "FREAK" => "SSL_FREAK",
        "DROWN" => "SSL_DROWN",
        "LOGJAM" => "SSL_LOGJAM",
        "LOGJAM-common_primes" => "SSL_LOGJAM",
        "BEAST_CBC_TLS1" => "SSL_BEAST",
        "BEAST" => "SSL_BEAST",
        # "LUCKY13" => "SSL_LUCKY13",
        "RC4" => "SSL_RC4",

        // Protocols
        "TLS1" => "SSL_TLS_1_0",
        "TLS1_1" => "SSL_TLS_1_1",
    ];

    /**
     * Path to the temporary output file.
     *
     * @var string
     */
    protected $tmp;

    /*
     * {@inheritdoc}
     */
    protected function init()
    {
        $this->tmp = 'outputs/testssl_' . $this->model->name . '.json';
    }

    /*
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
            return false;
        }

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
        if ($this->environment != 'local' || !Storage::exists($this->tmp)) {
            $this->runProcess([env('TOOLS_TESTSSL'), '-p', '-U', '--mode', 'parallel', '--jsonfile', storage_path('app/' . $this->tmp), $this->model->name]);
        }
        $content = Storage::get($this->tmp);
        if ($this->environment != 'local') {
            Storage::delete($this->tmp);
        }
        $this->store($content);
    }

    /**
     * Store the obtained data.
     *
     * @param array $content
     */
    private function store($content)
    {
        $content = json_decode($content);
        foreach ($content as $entry) {
            if (!isset($entry->severity)) {
                continue;
            }

            if ($entry->severity == 'OK') {
                continue;
            }

            // Non-vulnerabilities (protocols)
            if (!isset($entry->cwe) && strstr($entry->finding, 'not offered')) {
                continue;
            }

            $vulnerabilityType = $this->getVulnerabilityTypeFromId($entry->id);
            if (!$vulnerabilityType) {
                continue;
            }

            $this->items[] = $this->storeFinding($this->model, $this->model->certificate, null, $vulnerabilityType, isset($entry->finding) ? $entry->finding : null);
        }
    }

    /**
     * Get the associated vulnerability Type by the output finding ID.
     *
     * @param  string $id
     * @return \App\Models\VulnerabilityType|void
     */
    private function getVulnerabilityTypeFromId($id)
    {
        if (isset(self::TYPES_CODES[$id])) {
            return VulnerabilityType::whereCode(self::TYPES_CODES[$id])->first();
        }
    }
}
