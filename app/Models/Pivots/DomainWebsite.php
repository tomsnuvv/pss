<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;
use App\Libs\Helpers\Projects;

class DomainWebsite extends Pivot
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        // Attach the Domains into Website's Projects, and the opposite way
        static::created(function ($model) {
            Projects::relateProjectsFromSourceToTarget($model->website, $model->domain);
            Projects::relateProjectsFromSourceToTarget($model->domain, $model->website);
        });
    }

    /**
     * Domain relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function domain()
    {
        return $this->belongsTo('App\Models\Domain');
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
