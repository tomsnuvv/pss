<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Libs\Helpers\Projects;

class HostWebsite extends Pivot
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        // Attach the Host into Website's Projects, but not the opposite way
        static::created(function ($model) {
            Projects::relateProjectsFromSourceToTarget($model->website, $model->host);
        });
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

    /**
     * Website relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function website()
    {
        return $this->belongsTo('App\Models\Website');
    }
}
