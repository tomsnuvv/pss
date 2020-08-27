<?php

namespace App\Events\Contracts\Interfaces;

use Illuminate\Database\Eloquent\Model;

/**
 * Model Event interface class.
 */
interface ModelEvent
{
    /**
     * Create a new event instance.
     *
     * @param \Illuminate\Database\Eloquent\Model $model
     * @return void
     */
    public function __construct(Model $model);
}
