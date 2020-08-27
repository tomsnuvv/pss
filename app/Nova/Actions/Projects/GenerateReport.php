<?php

namespace App\Nova\Actions\Projects;

use App\Models\Environment;
use App\Models\Project;
use App\Models\Severity;
use App\Models\Website;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\View;
use Laravel\Nova\Actions\Action;
use Laravel\Nova\Actions\Actionable;
use Laravel\Nova\Fields\ActionFields;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Select;
use Spatie\Browsershot\Browsershot;

class GenerateReport extends Action
{
    use Actionable;

    /**
     * Indicates if this action is only available on the resource detail view.
     *
     * @var bool
     */
    public $onlyOnDetail = true;

    /**
     * Report storage path.
     *
     * @var string
     */
    const STORAGE_PATH = 'reports/';

    /**
     * Perform the action on the given models.
     *
     * @param  \Laravel\Nova\Fields\ActionFields  $fields
     * @param  \Illuminate\Support\Collection  $models
     * @return array|string[]
     * @throws \Spatie\Browsershot\Exceptions\CouldNotTakeBrowsershot
     * @todo   Allow multiple projects ?
     */
    public function handle(ActionFields $fields, Collection $models)
    {
        foreach ($models as $project) {
            $findings = $this->generateFindings($project, $fields);
            $scope = $this->generateScope($project, $fields);
            $severity = count($findings) ? $findings[0]->severity->name : 'Safe';
            $stats = $this->generateStats($findings);

            $render = (string) View::make('projects.reports.index',
                compact(['project', 'findings', 'scope', 'severity', 'stats']));

            $this->createStorage();

            if ($fields->format == 'HTML') {
                $ext = '.html';
                Storage::put(self::STORAGE_PATH.$project->id, $render);
            } else {
                $ext = '.pdf';
                Browsershot::html($render)
                    ->noSandbox()
                    ->format('A4')
                    ->margins(10, 0, 10, 0)
                    ->setNodeBinary(env('TOOLS_NODE'))
                    ->setNpmBinary(env('TOOLS_NPM'))
                    ->savePdf(storage_path('app/'.self::STORAGE_PATH.$project->id));
            }

            return Action::download(route('download.report', $project->id), '');
        }
    }

    /**
     * Create the output storage.
     */
    private function createStorage()
    {
        $path = storage_path('app/'.self::STORAGE_PATH);
        File::isDirectory($path) or File::makeDirectory($path, 0777, true, true);
    }

    /**
     * Get the fields available on the action.
     *
     * @return array
     */
    public function fields()
    {
        return [
            Boolean::make('Production')->help(
                'Include findings from production Domains & Hosts.'
            )->withMeta(['value' => 1]),
            Select::make('Environment')->options([0 => 'All'] + Environment::pluck('name', 'id')->toArray())->help(
                'Include ONLY findings from a certain Website environment.'
            )->withMeta(['value' => 7]),
            Select::make('Format')->options(['PDF' => 'PDF', 'HTML' => 'HTML'])->withMeta(['value' => 'PDF']),
        ];
    }

    /**
     * Check if a Website environment was selected in the fields.
     *
     * @param  |Illuminate\Database\Eloquent\Model $model
     * @param  int  $environmentId
     * @return bool
     */
    private function hasSelectedEnvironment(Website $website, $environmentId)
    {
        return !$website->environment || $website->environment->id == $environmentId;
    }

    /**
     * Check if a Model (Host or Domain) has production Websites.
     *
     * @param  |Illuminate\Database\Eloquent\Model $model
     * @return bool
     */
    private function hasProductionWebsites($model)
    {
        return $model->websites()->where('environment_id', Environment::PRODUCTION)->exists();
    }

    /**
     * Generate the list of findings.
     *
     * @param  \App\Models\Project  $project
     * @param  ActionFields           s$fields
     * @return \App\Models\Findings[]
     */
    private function generateFindings(Project $project, ActionFields $fields)
    {
        $findings = [];

        foreach ($project->findings()->open()->orderBy('severity_id', 'DESC')->get() as $finding) {
            $class = str_replace('App\\Models\\', '', get_class($finding->target));

            // Websites must have the selected environment
            if ($class == 'Website') {
                if ($fields->environment && !$this->hasSelectedEnvironment($finding->target, $fields->environment)) {
                    continue;
                }
            } // If an installation source is a Website, that website environment must match the selected environment
            elseif ($class == 'Installation') {
                $installation = $finding->target;
                if ($fields->environment && get_class($installation->source) == 'App\\Models\\Website' && !$this->hasSelectedEnvironment($installation->source,
                        $fields->environment)) {
                    continue;
                }
            } // Only production domains / hosts (if selected)
            elseif ($class == 'Domain' || $class == 'Host') {
                if (!$fields->production || !$this->hasProductionWebsites($finding->target)) {
                    continue;
                }
            }

            $findings[] = $finding;
        }

        return $findings;
    }

    /**
     * Generate the scope.
     *
     * @param  \App\Models\Project  $project
     * @param  ActionFields           s$fields
     * @return array
     */
    private function generateScope(Project $project, ActionFields $fields)
    {
        $scope = [];

        foreach ($project->domains as $domain) {
            if (!$fields->production || !$this->hasProductionWebsites($domain)) {
                continue;
            }
            $scope['domains'][] = $domain->name;
        }

        foreach ($project->websites as $website) {
            if (!$this->hasSelectedEnvironment($website, $fields->environment)) {
                continue;
            }
            $scope['websites'][] = $website->url;
        }

        foreach ($project->hosts as $host) {
            if (!$fields->production || !$this->hasProductionWebsites($host)) {
                continue;
            }
            $scope['hosts'][] = $host->name ?: $host->ip;
        }

        foreach ($project->repositories as $repository) {
            $scope['repositories'][] = $repository->name;
        }

        return $scope;
    }

    /**
     * Generate the report summary stats.
     *
     * @param  \App\Models\Findings[]  $findings
     * @return array
     */
    private function generateStats($findings)
    {
        $severities = [];
        $types = [];
        foreach (Severity::all() as $severity) {
            $severities[$severity->name] = 0;
        }
        foreach ($findings as $finding) {
            if ($finding->severity) {
                $severities[$finding->severity->name]++;
            }
            if ($finding->type) {
                if (isset($types[$finding->type->name])) {
                    $types[$finding->type->name]++;
                } else {
                    $types[$finding->type->name] = 1;
                }
            }
        }
        $stats['severities'] = $severities;
        $stats['types'] = $types;

        return $stats;
    }
}
