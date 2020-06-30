<?php

namespace App\Repositories;

interface RepositoryInterface
{
    /**
     * @param array $filter
     *
     * @return mixed
     */
    public function getItems(array $filter = []);
}
