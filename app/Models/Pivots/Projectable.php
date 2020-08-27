<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\MorphPivot;
use App\Observers\ProjectableObserver;

class Projectable extends MorphPivot
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        static::observe(ProjectableObserver::class);
    }

    /**
     * Project relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function project()
    {
        return $this->belongsTo('App\Models\Project');
    }

    /**
     * Projectable relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function projectable()
    {
        return $this->morphTo();
    }
}
