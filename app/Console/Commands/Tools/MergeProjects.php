<?php

namespace App\Console\Commands\Tools;

use App\Libs\Helpers\Projects;
use App\Models\Project;
use Illuminate\Console\Command;

class MergeProjects extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tools:merge-projects {base} {merged}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Merges a (merged) project into a base project (base). The merged project will be deleted.';

    /**
     * Project relations.
     *
     * @var array
     */
    const RELATIONS = [
        'organisations',
        'domains',
        'certificates',
        'websites',
        'hosts',
        'ports',
        'repositories',
        'installations',
        'findings'
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $base = Project::findOrFail($this->argument('base'));
        $merge = Project::findOrFail($this->argument('merged'));

        foreach (self::RELATIONS as $relation) {
            foreach ($merge->$relation()->get() as $model) {
                Projects::attachProjectIntoModel($model, $base->id);
            }
        }

        $merge->delete();
    }
}
