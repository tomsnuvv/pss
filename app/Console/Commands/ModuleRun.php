<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Module;

/**
 * Module Run command.
 *
 * Executes a single module.
 */
class ModuleRun extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module:run {module} {model?} {id?} {--force} {--rand} {--failed}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Runs a module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $force = $this->option('force');
        $rand = $this->option('rand');
        $failed = $this->option('failed');
        $modelName = $this->argument('model');
        $moduleName = $this->argument('module');

        $moduleModel = Module::where('code', $moduleName)->first();
        if (!$moduleModel) {
            throw new \Exception('Module ' . $moduleName . ' not found. Make sure it\'s imported in the seeders');
        }

        if ($force) {
            $moduleModel->logs()->delete();
        }

        $moduleClass = 'App\\Libs\\Modules\\' . $moduleName;
        if ($this->argument('model')) {
            $modelClass = 'App\\Models\\' . $modelName;
            if ($this->argument('id')) {
                $model = $modelClass::findOrFail($this->argument('id'));
                $this->showTitle($model);
                $module = new $moduleClass($model, $this->output);
                $module->execute();
            } else {
                $query = $modelClass::query();
                if (in_array($modelName, ['Website', 'Host', 'Domain'])) {
                    $query->where('key', 1);
                }
                if ($failed) {
                    $query->whereHas('moduleLogs', function (Builder $query) use ($moduleModel) {
                        $query->where('module_id', $moduleModel->id)->whereHas('status', function (Builder $query) {
                            $query->error();
                        });
                    });
                }
                if ($rand) {
                    $query = $query->inRandomOrder();
                }
                foreach ($query->get() as $model) {
                    $this->showTitle($model);
                    $module = new $moduleClass($model, $this->output);
                    $module->execute();
                    unset($module);
                    unset($model);
                }
            }
        } else {
            $module = new $moduleClass(null, $this->output);
            $module->execute();
        }
    }

    /**
     * Show model title.
     *
     * @param  object $model
     */
    private function showTitle($model)
    {
        $title = $model->id;
        if (isset($model->name) && $model->name) {
            $title .= ' (' . $model->name . ')';
        } elseif (isset($model->url)) {
            $title .= ' (' . $model->url . ')';
        } elseif (isset($model->ip)) {
            $title .= ' (' . $model->ip . ')';
        }

        $this->output->title('ID: ' . $title);
    }
}
