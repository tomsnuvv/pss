<?php

namespace App\Libs\Providers\Repositories;

use Github\Client as GithubClient;
use Github\ResultPager as GithubClientPaginator;
use App\Libs\Contracts\Providers\Abstracts\Repositories;
use App\Models\Integration;

/**
 * Github Provider.
 *
 * Interacts with GitHub repositories.
 * Allows to see if a certain repository exist, and to extract information
 * such as latest release version etc.
 *
 * Uses https://github.com/KnpLabs/php-github-api as client.
 */
class Github extends Repositories
{
    /**
     * Github Client Paginator.
     *
     * @var \Github\ResultPager
     */
    private $paginator;

    /**
     * Account Token.
     *
     * @var string
     */
    private $token;

    /**
     * Organization names.
     *
     * @var array
     */
    private $organizations;

    /**
     * Initializate the Github Client.
     */
    public function init()
    {
        $integration = Integration::ofType('Github')->first();

        $this->token = $integration->token;
        $this->client = new GithubClient();
        $this->auth();
        $this->paginator = new GithubClientPaginator($this->client);
        $this->organizations = $integration->settings['orgs'];
    }

    /**
     * Auth the Github client.
     *
     * @return bool
     */
    private function auth()
    {
        return $this->client->authenticate($this->token, null, GithubClient::AUTH_HTTP_TOKEN);
    }

    /**
     * {@inheritdoc}
     */
    public function getRepositories()
    {

        $repos = [];
        foreach ($this->organizations as $organization) {
            $repos = array_merge($repos, $this->paginator->fetchAll($this->client->api('organizations'), 'repositories', [$organization]));
        }

        return $repos;
    }

    /**
     * {@inheritdoc}
     */
    public function getRepository()
    {
        try {
            return $this->client->api('repo')->show($this->repository->user, $this->repository->repo);
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLatestRelease()
    {
        try {
            $release = $this->client->api('repo')->releases()->latest($this->repository->user, $this->repository->repo);

            return isset($release['tag_name']) ? $release['tag_name'] : null;
        } catch (\Exception $e) {
            return;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function downloadFile($file, $ref = 'master')
    {
        return $this->client->api('repo')->contents()->download($this->repository->user, $this->repository->repo, $file, $ref);
    }

    /**
     * {@inheritdoc}
     */
    public function searchFile($filename)
    {
        return $this->client->api('search')->code('filename:' .$filename. ' repo:' . $this->repository->user. '/' . $this->repository->repo);
    }

    /**
     * {@inheritdoc}
     */
    public function fileExist($path)
    {
        return $this->client->api('repo')->contents()->exists($this->repository->user, $this->repository->repo, $path);
    }
}
