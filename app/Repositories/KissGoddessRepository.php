<?php

namespace App\Repositories;

use App\Models\KissGoddess;
use App\Traits\Jav\HasOrdering;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Request;

class KissGoddessRepository
{
    use HasOrdering;

    public function getItems(Request $request): LengthAwarePaginator
    {
        $builder = app(KissGoddess::class)->newQuery();

        if ($keyword = $request->get(ConfigRepository::KEY_KEYWORD)) {
            $builder->orWhere(KissGoddess::TITLE, 'LIKE', '%'.$keyword.'%');
        }

        $this->processOrdering($builder, $request);

        return $builder
            ->paginate((int) $request->get(ConfigRepository::KEY_PER_PAGE, ConfigRepository::DEFAULT_PER_PAGE))
            ->appends(request()->except(ConfigRepository::KEY_PAGE, '_token'));
    }

    public function findById(string $id): ?KissGoddess
    {
        return KissGoddess::find($id);
    }
}
