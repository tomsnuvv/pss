<?php

namespace App\Libs\Modules\Discovery\Websites;

use App\Libs\Contracts\Modules\Abstracts\Module;
use Spatie\Browsershot\Browsershot as BrowsershotLib;
use Illuminate\Support\Facades\Storage;
use \Exception;

/**
 * Browsershot Websites Discovery Module.
 *
 * Obtains an image snapshot from the website content.
 */
class Browsershot extends Module
{
    /**
     * Storage path for the snapshots.
     *
     * @var string
     */
    const PATH = 'public/websites/snapshots';

    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        if ($this->ranInLastHours(168)) {
            $this->setMessage('Module already executed in the last week');
            return false;
        }

        if ($this->model->status === null) {
            $this->setMessage('Website without status');
            return false;
        }


        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        Storage::makeDirectory(self::PATH);
        $imagePath = 'app/' . self::PATH . '/' . $this->model->id . '.png';

        try {
            BrowsershotLib::url($this->model->url)->ignoreHttpsErrors()->noSandbox()->save(storage_path($imagePath));
        } catch (Exception $e) {
            // Accept timeouts
            if (strstr($e->getMessage(), 'Navigation timeout')) {
                return;
            }
            // Domain not resolved
            if (strstr($e->getMessage(), 'ERR_NAME_NOT_RESOLVED')) {
                return;
            }
            throw $e;
        }

        $this->outputDetail('Snapshot stored in', $imagePath);
    }
}
