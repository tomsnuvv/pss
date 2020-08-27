<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Module Log Model.
 *
 * Stores when a module execution information.
 */
class ModuleLog extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['status', 'model_type', 'model_id', 'results', 'details'];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['executed_at', 'finished_at'];

    /**
     * Use default timestamps fields.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * Boot events.
     */
    public static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if ($model->executed_at && $model->finished_at) {
                $model->duration = $model->finished_at->diffInSeconds($model->executed_at);
            }
        });
    }

    /**
     * Marks the log as executed.
     */
    public function markAsExecuted()
    {
        $this->status()->associate(ModuleLogStatus::executed()->first());
        $this->executed_at = Carbon::now();
        $this->finished_at = null;
        $this->results = null;
        $this->details = null;
        $this->save();
    }

    /**
     * Marks the log as finished.
     *
     * @param int    $results
     * @param string $details
     */
    public function markAsFinished($results = null, $details = null)
    {
        $this->status()->associate(ModuleLogStatus::finished()->first());
        $this->results = $results;
        $this->finished_at = Carbon::now();
        $this->setDetails($details);
        $this->save();
    }

    /**
     * Marks the log as aborted.
     *
     * @param string $details
     */
    public function markAsCantRun($details = null)
    {
        $this->status()->associate(ModuleLogStatus::cantRun()->first());
        $this->setDetails($details);
        $this->save();
    }

    /**
     * Marks the log as aborted.
     *
     * @param string $details
     */
    public function markAsError($details = null)
    {
        $this->status()->associate(ModuleLogStatus::error()->first());
        $this->setDetails($details);
        $this->finished_at = Carbon::now();
        $this->save();
    }

    /**
     * Set the details field.
     *
     * @param string $details
     */
    public function setDetails($details = null)
    {
        $this->details = substr($details, 0, 2000);
    }

    /**
     * Filters findings with open status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder $query
     * @param  \Illuminate\Database\Eloquent\Model   $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfModel(Builder $query, $model)
    {
        return $query->where('model_type', get_class($model))->where('model_id', $model->id);
    }

    /**
     * Status relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function status()
    {
        return $this->belongsTo('App\Models\ModuleLogStatus');
    }

    /**
     * Get all of the related model.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function model()
    {
        return $this->morphTo();
    }

    /**
     * Module relation
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function module()
    {
        return $this->belongsTo('App\Models\Module');
    }
}
