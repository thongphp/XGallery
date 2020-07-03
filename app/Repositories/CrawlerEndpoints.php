<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

class CrawlerEndpoints extends BaseRepository
{
    public function __construct(\App\Models\CrawlerEndpoints $model)
    {
        parent::__construct($model);
    }

    public function getWorkingItem(string $name): \App\Models\CrawlerEndpoints
    {
        /** @var Collection $items */
        return $this->builder->where(['crawler'=>$name])->orderBy('updated_at', 'asc')->first();
    }
}
