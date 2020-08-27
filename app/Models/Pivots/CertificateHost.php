<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Libs\Helpers\Projects;

class CertificateHost extends Pivot
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        // Attach the Certificate into Host's Projects, but not the opposite way
        static::created(function ($model) {
            Projects::relateProjectsFromSourceToTarget($model->host, $model->certificate);
        });
    }

    /**
     * Certificate relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function certificate()
    {
        return $this->belongsTo('App\Models\Certificate');
    }

    /**
     * Host relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function host()
    {
        return $this->belongsTo('App\Models\Host');
    }
}
