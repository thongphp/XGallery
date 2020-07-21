<?php

namespace App\Repositories;

use App\Models\XiurenModel;
use App\Traits\Jav\HasOrdering;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Request;

class XiurenRepository
{
    use HasOrdering;

    public function getItems(Request $request): LengthAwarePaginator
    {
        $builder = app(XiurenModel::class)->newQuery();

        $this->processOrdering($builder, $request);

        return $builder
            ->paginate((int) $request->get(ConfigRepository::KEY_PER_PAGE, ConfigRepository::DEFAULT_PER_PAGE))
            ->appends(request()->except(ConfigRepository::KEY_PAGE, '_token'));
    }

    public function findById(string $id): ?XiurenModel
    {
        return XiurenModel::find($id);
    }
}
