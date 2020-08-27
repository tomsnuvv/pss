<?php

namespace App\Libs\Providers\Vulnerabilities\Composer;

use Symfony\Component\Yaml\Yaml;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * SecurityAdvisories Composer Vulnerabilities Provider.
 *
 * Interacts with SecurityAdvisories PHP security advisory database.
 * https://github.com/FriendsOfPHP/security-advisories
 */
class SecurityAdvisories
{
    /**
     * Repository URL
     *
     * @var string
     */
    const REPOSITORY = 'https://github.com/FriendsOfPHP/security-advisories.git';

    /**
     * Library path
     *
     * @var string
     */
    const LIBRARY_PATH = 'storage/app/providers/vulnerabilities/security-advisories';

    /**
     * Ignored subfolders
     *
     * @var array
     */
    const IGNORED = ['.', '..', '.git', 'vendor'];

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
        $this->parse();

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
     */
    private function parse()
    {
        $files = scandir(self::LIBRARY_PATH);
        foreach ($files as $vendor) {
            $fullPath = self::LIBRARY_PATH . '/' . $vendor;
            if (is_dir($fullPath) && !in_array($vendor, self::IGNORED)) {
                $this->parseProducts($vendor);
            }
        }
    }

    /**
     * Parses the products from the library repository.
     *
     * @param string $vendor
     */
    private function parseProducts($vendor)
    {
        $path = self::LIBRARY_PATH . '/' . $vendor;
        $files = scandir($path);
        foreach ($files as $product) {
            $fullPath = $path . '/' . $product;
            if (is_dir($fullPath) && !in_array($product, self::IGNORED)) {
                $this->parseAdvisories($vendor, $product);
            }
        }
    }

    /**
     * Parses the advisories from the library repository.
     *
     * @param string $vendor
     * @param string $product
     */
    private function parseAdvisories($vendor, $product)
    {
        $path = self::LIBRARY_PATH . '/' . $vendor . '/' . $product;
        $files = scandir($path);
        foreach ($files as $file) {
            $fullPath = $path . '/' . $file;
            if (is_file($fullPath)) {
                $this->parseAdvisory($vendor, $product, $file);
            }
        }
    }

    /**
     * Parses an advisory from the library repository.
     *
     * @param string $vendor
     * @param string $product
     * @param string $file
     */
    private function parseAdvisory($vendor, $product, $file)
    {
        $path = self::LIBRARY_PATH . '/' . $vendor . '/' . $product . '/' . $file;

        $content = Yaml::parse(file_get_contents($path));

        $this->advisories[$vendor][$product][] = $content;
    }
}
