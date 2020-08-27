<?php

namespace App\Libs\Contracts\Modules\Abstracts;

use App\Libs\Contracts\Modules\Interfaces\Module as ModuleInterface;
use \Illuminate\Database\Eloquent\Model;
use Symfony\Component\Console\Output\OutputInterface;
use App\Models\Module as ModuleModel;
use App\Models\ModuleLogStatus;
use \Exception;
use Illuminate\Support\Facades\App;

/**
 * Module abstract.
 */
abstract class Module implements ModuleInterface
{
    /**
     * Module Model.
     *
     * @var \App\Models\Module
     */
    protected $module;

    /**
     * Model used (if any) to run the module.
     *
     * @var \Illuminate\Database\Eloquent\Model
     */
    protected $model;

    /**
     * ModuleLog Model.
     *
     * @var \App\Models\ModuleLog
     */
    protected $log;

    /**
     * Items.
     *
     * Store the items spawned by the module.
     *
     * @var \Illuminate\Database\Eloquent\Model[]
     */
    protected $items = [];

    /**
     * The output interface implementation.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Module message.
     *
     * Used to store the can't run reason,
     * or any other details that will be stored in the log.
     *
     * @var string
     */
    protected $message;

    /**
     * Current environment.
     *
     * @var string
     */
    protected $environment;

    /**
     * @param \Illuminate\Database\Eloquent\Model                    $model
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     */
    public function __construct(Model $model = null, OutputInterface $output = null)
    {
        $this->model = $model;
        $this->module = $this->getModuleModel();
        $this->output = $output;
        $this->environment = App::environment();
    }

    /**
     * Executes the module
     */
    public function execute()
    {
        $this->output(' [+] Executing <info>' . $this->getCode() . '</info> module...');
        $this->createLog();

        $this->init();

        // Modules only run in key items
        if (isset($this->model->key) && $this->model->key === 0) {
            $this->setMessage('Not a key model');
            $this->outputDetail('Can\'t run', $this->message);
            $this->log->markAsCantRun($this->message);
            $this->finishWithoutRun();
            return;
        }

        if (!$this->canRun()) {
            $this->outputDetail('Can\'t run', $this->message);
            $this->log->markAsCantRun($this->message);
            $this->finishWithoutRun();
            return;
        }

        $this->log->markAsExecuted();

        try {
            $this->run();
        } catch (Exception $e) {
            $this->outputError($e->getMessage());
            $this->log->markAsError($e->getMessage());
            return;
        }

        $this->finish();

        $this->log->markAsFinished(count($this->items), $this->message);
    }

    /**
     * Checks if the the module can run.
     */
    protected function canRun()
    {
        return true;
    }

    /**
     * Perfoms the module initialization.
     *
     * Usefull for performing actions before the module is executed.
     */
    protected function init()
    {
        // Does nothing
    }

    /**
     * Performs the main module action.
     *
     * @throws \Exception
     */
    protected function run()
    {
        // Does nothing
    }

    /**
     * Perfoms the afet-run actions.
     *
     * Useful to perform actions once the module was executed.
     */
    protected function finish()
    {
        // Does nothing
    }

    /**
     * Perfoms the afet-run actions when a module can't run.
     */
    protected function finishWithoutRun()
    {
        // Does nothing
    }

    /**
     * Find (or create) a module log.
     */
    protected function createLog()
    {
        $data = [];

        if ($this->model) {
            $data = [
                'model_type' => get_class($this->model),
                'model_id' => $this->model->id,
            ];
        }

        $this->log = $this->module->logs()->firstOrCreate($data);
    }

    /**
     * Check if the module ran in the last X hours.
     *
     * @param  int $hours
     * @return bool
     */
    protected function ranInLastHours($hours)
    {
        if ($this->log && $this->log->executed_at != null && in_array($this->log->status_id, [ModuleLogStatus::FINISHED, ModuleLogStatus::NORUN])) {
            $last = $this->log->executed_at->diffInHours();
            return $last <= $hours;
        }

        return false;
    }

    /**
     * Gets the module code.
     *
     * @return string
     */
    public function getCode()
    {
        return str_replace('App\\Libs\\Modules\\', '', get_class($this));
    }

    /**
     * Get the related Module Model.
     *
     * @throws \Exception Module not found
     * @return \App\Models\Module
     */
    public function getModuleModel()
    {
        if ($this->module) {
            return $this->module;
        }
        $this->module = ModuleModel::whereCode($this->getCode())->first();
        if (!$this->module) {
            throw new Exception('Module ' . $this->getCode() . ' not found. Make sure it\'s imported in the seeders');
        }

        return $this->module;
    }

    /**
     * Deletes the items from the provided relationship (on the current model),
     * that were not generated by the current run of the module.
     *
     * If the relationship items are findings or installations,
     * will only delete the ones created by the current module
     *
     * @param  string $relationship Relationship name
     */
    protected function deleteOldItems($relationship)
    {
        $query = $this->model->$relationship();

        if ($relationship == 'findings' || $relationship == 'installations') {
            $query = $query->where('module_id', $this->module->id);
        }

        if (!empty($this->items)) {
            $query->whereNotIn('id', array_column($this->items, 'id'))->delete();
        } else {
            $query->delete();
        }
    }

    /**
     * Writes an output line.
     *
     * @param string $text
     */
    protected function output($text)
    {
        if ($this->output) {
            $this->output->writeln($text);
        }
    }

    /**
     * Writes the output for a detail.
     *
     * @param string $field
     * @param string $value
     */
    protected function outputDetail($field, $value)
    {
        $this->output('  - <comment>' . ucfirst(str_replace('_', ' ', $field)) . ':</comment> ' . $value);
    }

    /**
     * Writes an error message.
     *
     * @param string $message
     */
    protected function outputError($message)
    {
        if ($this->output) {
            $this->output->error($message);
        }
        $this->setMessage($message);
    }

    /**
     * Sets the message.
     *
     * @param string $message
     */
    protected function setMessage($message)
    {
        $this->message = utf8_decode($message);
    }

    /**
     * Writes the output from a data array.
     *
     * @param array $data
     */
    protected function outputData(array $data)
    {
        foreach ($data as $field => $value) {
            if ($field == 'raw') {
                continue;
            }
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            $this->outputDetail($field, $value);
        }
    }

    /**
     * Show the current used memory.
     */
    protected function outputUsedMemory()
    {
        $unit = array('b','kb','mb','gb','tb','pb');
        $size = memory_get_usage(true);
        $memory = @round($size/pow(1024, ($i=floor(log($size, 1024)))), 2).' '.$unit[$i];

        $this->output->writeln(' -- ' . $memory . ' --');
    }
}
