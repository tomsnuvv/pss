<?php

namespace App\Libs\Modules\Import\Repositories;

use App\Libs\Contracts\Modules\Abstracts\Module;
use App\Libs\Providers\Repositories\Github as Provider;
use App\Models\Repository;
use App\Models\Integration;

/**
 * Github Repositories Import Module.
 *
 * Obtains all the Github repositories from the organisations.
 * The organisations are defined in config/libs.php
 */
class Github extends Module
{
    /**
     * {@inheritdoc}
     */
    protected function canRun()
    {
        $integration = Integration::ofType('Github')->first();
        if (!$integration) {
            $this->setMessage('Github Integration missing');
            return false;
        }

        if (!isset($integration->settings['orgs'])) {
            $this->setMessage('Github organisations missing');
            return false;
        } elseif (!is_array($integration->settings['orgs']) || !count($integration->settings['orgs'])) {
            $this->setMessage('Github organisations malformed');
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $provider = new Provider();

        $repositories = $provider->getRepositories();
        if ($repositories !== null && count($repositories)) {
            $this->store($repositories);
            // $this->deleteOldRepositories();
            $this->showOutput();
        }
    }

    /**
     * Store the obtained data.
     *
     * @param array $repositories
     */
    private function store($repositories)
    {
        foreach ($repositories as $repository) {
            if ($repository['archived'] || $repository['disabled']) {
                // Delete if exists
                /*$repo = Repository::whereUrl($repository['html_url'])->first();
                if ($repo) {
                    $this->outputDetail('Deleted', $repo->name);
                    $repo->delete();
                }*/
                continue;
            }

            $item = Repository::firstOrNew([
                'url'  => $repository['html_url'],
            ]);
            $item->name = $repository['full_name'];
            $item->clone_url = $repository['ssh_url'] ?: $repository['clone_url'];
            $item->public = $repository['private'] ? 0 : 1;
            $item->save();
            $this->items[] = $item;
        }
    }

    /**
     * Delete old repositories.
     */
    private function deleteOldRepositories()
    {
        $repositories = Repository::where('url', 'like', '%github.com%')->whereNotIn('id', array_column($this->items, 'id'))->get();
        foreach ($repositories as $repository) {
            $this->outputDetail('Deleted', $repository->name);
            $repository->delete();
        }
    }

    /**
     * Display the obtained data.
     */
    private function showOutput()
    {
        foreach ($this->items as $item) {
            $this->outputDetail('Repository', $item->name);
        }
    }
}
