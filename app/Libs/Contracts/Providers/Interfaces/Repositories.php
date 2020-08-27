<?php

namespace App\Libs\Contracts\Providers\Interfaces;

/**
 * Repositories Provider interface.
 */
interface Repositories
{
    /**
     * Get all the repositories from the organizations.
     *
     * @return array
     */
    public function getRepositories();

    /**
     * Gets the repository details.
     *
     * @return array|void
     */
    public function getRepository();

    /**
     * Get the latest release.
     *
     * @return string|void
     */
    public function getLatestRelease();

    /**
     * Perform a file search.
     *
     * @param string $file
     *
     * @return array|null
     */
    public function searchFile($file);

    /**
     * Download a file.
     *
     * @param string $file
     * @param string $ref  The name of the commit/branch/tag
     *
     * @return string|null
     */
    public function downloadFile($file, $ref = 'master');

    /**
     * Checks if the repository has a certain file or folder.
     *
     * @param string $path
     *
     * @return bool
     */
    public function fileExist($path);
}
