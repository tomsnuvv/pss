<?php

namespace App\Libs\Contracts\Executors\Abstracts;

use App\Libs\Contracts\Executors\Interfaces\Executor as ExecutorInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Illuminate\Database\Eloquent\Model;

/**
 * Executor Abstract class.
 *
 * Executes a battery of modules.
 */
abstract class Executor implements ExecutorInterface
{
    /**
     * Model used (if any) to run the module.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * The output interface implementation.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     */
    public function __construct(Model $model = null, OutputInterface $output = null)
    {
        $this->model = $model;
        $this->output = $output;
    }

    /**
     * Run all the modules
     */
    public function run()
    {
        foreach (static::MODULES as $module) {
            $this->executeModule($module, $this->model);
        }

        foreach (static::MODULES_RELATIONS as $relation => $modules) {
            foreach ($modules as $module) {
                $relationModel = $this->model->$relation;
                if (!$relationModel) {
                    continue;
                }
                if (!is_a($relationModel, Model::class)) {
                    foreach ($relationModel as $model) {
                        $this->executeModule($module, $model);
                    }
                } else {
                    $this->executeModule($module, $relationModel);
                }
            }
        }
    }

    /**
     * Execute a module.
     *
     * @param  string $module
     * @param  \Illuminate\Database\Eloquent\Model $model
     */
    private function executeModule($module, Model $model = null)
    {
        $module = new $module($model, $this->output);
        $module->execute();
    }
}
