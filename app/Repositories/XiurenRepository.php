<?php

namespace App\Repositories;

use App\Models\Xiuren;
use App\Traits\Jav\HasOrdering;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class XiurenRepository
 * @package App\Repositories
 */
class XiurenRepository
{
    use HasOrdering;

    public function getItems(Request $request): LengthAwarePaginator
    {
        $builder = app(Xiuren::class)->newQuery();

        $this->processOrdering($builder, $request);

        return $builder
            ->paginate((int) $request->get(ConfigRepository::KEY_PER_PAGE, ConfigRepository::DEFAULT_PER_PAGE))
            ->appends(request()->except(ConfigRepository::KEY_PAGE, '_token'));
    }
}
