<?php

namespace App\Libs\Providers\Vulnerabilities\Javascript\Nodejs;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Node.js Security WG Javascript Vulnerabilities Provider.
 *
 * Interacts with Node.js Security Work Group Vulnerabilities database.
 * https://github.com/nodejs/security-wg/
 *
 * @todo Import core vulnerabilities?
 */
class SecurityWG
{
    /**
     * Repository URL
     *
     * @var string
     */
    const REPOSITORY = 'https://github.com/nodejs/security-wg.git';

    /**
     * Library path
     *
     * @var string
     */
    const LIBRARY_PATH = 'storage/app/providers/vulnerabilities/nodejs-security-wg';

    /**
     * Advisories
     *
     * @var array
     */
    private $advisories = [];

    /**
     * Gets all the vulnerabilities in the repository.
     */
    public function getVulnerabilities()
    {
        $this->update();
        $this->parse('npm');

        return $this->advisories;
    }

    /**
     * Updates the library repository.
     *
     * @return bool|void
     */
    private function update()
    {
        if (file_exists(self::LIBRARY_PATH.'/.git/')) {
            $process = new Process(['git', 'pull']);
            $process->setWorkingDirectory(self::LIBRARY_PATH);
        } else {
            $process = new Process(['git', 'clone', self::REPOSITORY, self::LIBRARY_PATH]);
        }

        $process->run();
        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return true;
    }

    /**
     * Parses the library into a vulnerabilities array.
     *
     * @param string $dir
     */
    private function parse($dir)
    {
        $files = glob(self::LIBRARY_PATH . '/vuln/' . $dir . '/*.json', GLOB_BRACE);
        foreach ($files as $file) {
            $this->parseFile($file);
        }
    }

    /**
     * Parses a advisory file.
     *
     * @param string $file
     */
    private function parseFile($file)
    {
        $json = json_decode(file_get_contents($file));
        $this->advisories[$json->module_name][] = $json;
    }
}
