<?php

namespace App\Libs\Contracts\Providers\Abstracts;

use App\Libs\Contracts\Providers\Interfaces\Repositories as RepositoriesInterface;
use App\Models\Repository;

/**
 * Repositories Provider abstract Class.
 */
abstract class Repositories implements RepositoriesInterface
{
    /**
     * Provider Client.
     *
     * @var mixed
     */
    protected $client;

    /**
     * Repository model.
     *
     * @var \App\Models\Repository
     */
    protected $repository;

    /**
     * @param \App\Models\Repository $repository
     */
    public function __construct(Repository $repository = null)
    {
        if ($repository !== null) {
            $this->setRepository($repository);
        }

        $this->init();
    }

    /**
     * Sets the repository model to interact with.
     *
     * @param \App\Models\Repository $repository
     */
    public function setRepository(Repository $repository)
    {
        $this->repository = $repository;
    }
}
