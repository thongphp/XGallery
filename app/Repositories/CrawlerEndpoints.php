<?php

namespace App\Repositories;

class CrawlerEndpoints extends BaseRepository
{
    public function __construct(\App\Models\CrawlerEndpoints $model)
    {
        parent::__construct($model);
    }

    public function getWorkingItem(string $name): \App\Models\CrawlerEndpoints
    {
        return $this->builder->where(['crawler'=>$name])->orderBy('updated_at', 'asc')->get()->first();
    }
}
