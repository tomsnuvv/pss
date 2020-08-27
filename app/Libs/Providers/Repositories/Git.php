<?php

namespace App\Libs\Providers\Repositories;

use Symfony\Component\Process\Process;
use App\Models\Repository;

/**
 * Git Provider.
 *
 * Interacts with Git repositories.
 */
class Git
{
    /**
     * Storage path
     *
     * @var string
     */
    const STORAGE_PATH = 'storage/app/providers/repositories';

    /**
     * Process timeout in seconds
     *
     * @var int
     */
    const TIMEOUT = 300;

    /**
     * Repository model.
     *
     * @var \App\Models\Repository
     */
    protected $repository;

    /**
     * @param \App\Models\Repository $repository
     */
    public function __construct(Repository $repository = null)
    {
        if ($repository !== null) {
            $this->setRepository($repository);
        }

        $this->init();
    }

    /**
     * Sets the repository model to interact with.
     *
     * @param \App\Models\Repository $repository
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * Clones or pulls the repository.
     *
     * @return bool
     */
    public function init()
    {
        if (file_exists($this->getLocalRepositoryPath() . '/.git/')) {
            return $this->pull();
        }

        return $this->clone();
    }

    /**
     * Get the local path where the repository is (or will be) stored.
     *
     * @return string
     */
    public function getLocalRepositoryPath()
    {
        $name = str_replace(['/', '../'], '-', $this->repository->name);

        return self::STORAGE_PATH . '/' . $name;
    }

    /**
     * Updates the library repository.
     *
     * @return bool
     */
    public function clone()
    {
        $process = new Process(['git', 'clone', $this->repository->clone_url, $this->getLocalRepositoryPath()]);
        $process->setTimeout(self::TIMEOUT);
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * Pull the repository.
     *
     * @return bool
     */
    public function pull()
    {
        $process = new Process(['git', 'pull']);
        $process->setTimeout(self::TIMEOUT);
        $process->setWorkingDirectory($this->getLocalRepositoryPath());
        $process->run();

        return $process->isSuccessful();
    }

    /**
     * Perform a file search.
     *
     * @param string $file File name to search
     *
     * @return array|void
     */
    public function searchFile($file)
    {
        $process = new Process(['find', $this->getLocalRepositoryPath(), '-name', $file]);
        $process->setTimeout(self::TIMEOUT);
        $process->run();

        if (!$process->isSuccessful()) {
            return;
        }

        $content = trim($process->getOutput(), PHP_EOL);
        if (!$content) {
            return;
        }

        return explode(PHP_EOL, $content);
    }

    /**
     * Delete the repository files.
     *
     * @return bool
     */
    public function delete()
    {
        $process = new Process(['rm', '-rf', $this->getLocalRepositoryPath()]);
        $process->setTimeout(self::TIMEOUT);
        $process->run();

        return $process->isSuccessful();
    }
}
