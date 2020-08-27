<?php

namespace App\Observers;

use App\Models\Certificate;

/**
 * Certificate Observer class.
 */
class CertificateObserver
{
    /**
     * Handle the Certificate "deleting" event.
     *
     * @param  \App\Models\Certificate $certificate
     * @return void
     */
    public function deleting(Certificate $certificate)
    {
        static::deleting(function ($certificate) {
            $certificate->findings()->delete();
            $certificate->projects()->detach();
            $certificate->moduleLogs()->delete();
        });
    }
}
