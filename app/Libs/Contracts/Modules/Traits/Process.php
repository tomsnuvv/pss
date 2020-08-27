<?php

namespace App\Libs\Contracts\Modules\Traits;

use Symfony\Component\Process\Process as ProcessClass;
use Symfony\Component\Process\Exception\ProcessFailedException;

/**
 * Process Trait for Modules
 */
trait Process
{
    /**
     * Run a command in a process
     *
     * @param  array $command The command to run and its arguments listed as separate entries
     * @param  bool  $ignoreFailure
     * @param  array $env Environment variables
     *
     * @throws \Symfony\Component\Process\Exception\ProcessFailedException
     *
     * @return string|null Output
     */
    protected function runProcess($command, $ignoreFailure = false, $env = null)
    {
        $process = new ProcessClass($command, null, $env);
        $process->setTimeout($this->getTimeout());
        $process->run();

        if (!$process->isSuccessful() && !$ignoreFailure) {
            throw new ProcessFailedException($process);
        }

        return $process->getOutput();
    }

    /**
     * Gets the defined timeout.
     *
     * Default
     *
     * @return int
     */
    protected function getTimeout()
    {
        if (isset($this->timeout)) {
            return $this->timeout;
        }

        return config('pss.modules.process.timeout');
    }
}
