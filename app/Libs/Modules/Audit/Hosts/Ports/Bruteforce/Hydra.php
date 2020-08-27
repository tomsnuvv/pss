<?php

namespace App\Libs\Modules\Audit\Hosts\Ports\Bruteforce;

use App\Libs\Contracts\Modules\Abstracts\Audit;
use App\Models\VulnerabilityType;
use Illuminate\Support\Facades\Storage;
use \App\Libs\Contracts\Modules\Traits\Process;
use Illuminate\Database\Eloquent\Builder;

/**
 * Hydra Host Ports Bruteforce Audit Module.
 *
 * Brute-forces services with default credentials using Hydra
 * https://github.com/vanhauser-thc/thc-hydra
 *
 * Uses BruteSpray wordlists:
 * https://github.com/x90skysn3k/brutespray/tree/master/wordlist
 */
class Hydra extends Audit
{
    use Process;

    /**
     * @inheritDoc
     */
    protected $vulnerabilityTypeCode = 'WEAK_PASSWORD';

    /**
     * Path to the temporary output file.
     *
     * @var string
     */
    protected $tmp;

    /**
     * Number of threats
     *
     * @var int
     */
    protected $threats = 16;

    /**
     * Path to the logins wordlist.
     *
     * @var string
     */
    protected $wordlistLogin;

    /**
     * Path to the passwords wordlist.
     *
     * @var string
     */
    protected $wordlistPass;

    /**
     * Timeout (in seconds).
     *
     * Set to 2h.
     *
     * @var int
     */
    protected $timeout = 7200;

    /**
     * Suitable services to bruteforce.
     *
     * @var array
     */
    const SERVICES = [
        'ssh', 'ftp', 'telnet', 'vnc', 'mssql', 'mysql', 'postgresql', 'rsh',
        'imap', 'nntp', 'pcanywhere','pop3',
        'rexec', 'rlogin', 'smbnt','smtp',
        'svn', 'vmauthd', 'snmp'
    ];

    /*
     * {@inheritdoc}
     */
    protected function init()
    {
        $dir ='outputs/hydra/' . $this->model->host->ip;
        Storage::makeDirectory($dir);
        $this->tmp = $dir . '/' . $this->model->port . '.txt';

        $this->wordlistLogin = 'wordlists/' . $this->model->service . '/user';
        $this->wordlistPass = 'wordlists/' . $this->model->service . '/password';

        if (in_array($this->model->service, ['ssh', 'mysql'])) {
            $this->threats = 4;
        }
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

        if (!$this->model->host) {
            $this->setMessage('Host no longer exists (orphan port)');
            return false;
        }


        if (!$this->model->host->key) {
            $this->setMessage('Host is not key');
            return false;
        }

        if (!in_array($this->model->service, self::SERVICES)) {
            $this->setMessage('Service <' . $this->model->service . '> is not suitable for bruteforce');
            return false;
        }

        if (!Storage::exists($this->wordlistLogin)) {
            $this->setMessage('Wordlist for service <' . $this->model->service . '> not defined');
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $target = $this->model->service . '://' . $this->model->host->ip . ':' . $this->model->port;

        $this->output('  -  Bruteforcing ' . $target . ' ...');
        if ($this->environment != 'local' || !Storage::exists($this->tmp)) {
            // Make sure output is removed, as Hydra appends
            Storage::delete($this->tmp);
            $this->runProcess([
                env('TOOLS_HYDRA'),
                '-L', storage_path('app/' . $this->wordlistLogin),
                '-P', storage_path('app/' . $this->wordlistPass),
                '-q', '-I', '-t', $this->threats,
                '-o', storage_path('app/' . $this->tmp),
                $target
            ]);
        }

        $output = Storage::get($this->tmp);
        if ($this->environment != 'local') {
            Storage::delete($this->tmp);
        }

        $this->store($output);
    }

    /**
     * Store the results
     *
     * @param string $output
     */
    private function store($output)
    {
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $output) as $line) {
            if (!$line) {
                continue;
            }

            if (!strstr($line, 'login: ')) {
                continue;
            }

            preg_match('/login: (.*?)   password: (.*?)$/', $line, $matches);
            if (!isset($matches[1]) || !isset($matches[2])) {
                continue;
            }
            $username = $matches[1];
            $password = $matches[2];

            $uid = crc32($username . ':' . $password);
            $details = 'Credentials found: ' . PHP_EOL . PHP_EOL . '**Username:** \'' . $username . '\'' . PHP_EOL . PHP_EOL .  '**Password:** \'' . $password . '\'';
            $this->storeFinding(null, null, null, null, $details, null, $uid);
        }
    }
}
