<?php


namespace App\Repositories;

use App\Models\Nhaccuatui;

class NhaccuatuiRepository extends BaseRepository
{
    public function __construct(Nhaccuatui $model)
    {
        parent::__construct($model);
    }

    public function getItems(array $filter = [])
    {
        if (isset($filter['title']) && !empty($filter['title'])) {
            $this->builder->where('name', 'LIKE', '%'.$filter['title'].'%');
        }
        return parent::getItems($filter); // TODO: Change the autogenerated stub
    }
}
