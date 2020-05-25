<?php

namespace App\Repositories;

use App\Models\Batdongsan;

class BatdongsanRepository extends BaseRepository
{
    public function __construct(Batdongsan $model)
    {
        parent::__construct($model);
    }

    public function getItems(array $filter = [])
    {
        if (isset($filter['email']) && !empty($filter['email'])) {
            $this->builder->where(['email' => $filter['email']]);
        }

        return parent::getItems($filter);
    }
}
