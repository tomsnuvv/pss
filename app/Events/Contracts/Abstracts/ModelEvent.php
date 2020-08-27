<?php

namespace App\Events\Contracts\Abstracts;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Database\Eloquent\Model;
use App\Events\Contracts\Interfaces\ModelEvent as ModelEventInterface;

/**
 * Model Event abstract class.
 */
abstract class ModelEvent implements ModelEventInterface
{
    use Dispatchable, SerializesModels;

    /**
     * Model.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    public $model;

    /*
     * {@inheritdoc}
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }
}
