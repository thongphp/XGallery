<?php

namespace App\Repositories;

use App\Models\Truyenchon\Truyenchon;
use App\Models\Truyenchon\TruyenchonChapter;
use App\Traits\Jav\HasOrdering;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TruyenchonRepository
 * @package App\Repositories
 */
class TruyenchonRepository
{
    use HasOrdering;

    public function getItems(Request $request): LengthAwarePaginator
    {
        $builder = app(Truyenchon::class)->newQuery();

        if ($keyword = $request->get(ConfigRepository::KEY_KEYWORD)) {
            $builder->orWhere(Truyenchon::TITLE, 'LIKE', '%'.$keyword.'%');
        }

        $this->processOrdering($builder, $request);

        return $builder
            ->paginate((int) $request->get(ConfigRepository::KEY_PER_PAGE, ConfigRepository::DEFAULT_PER_PAGE))
            ->appends(request()->except(ConfigRepository::KEY_PAGE, '_token'));
    }

    public function firstOrCreate(string $storyUrl, string $storyCover, string $storyTitle)
    {
        return Truyenchon::firstOrCreate(
            [
                'url' => $storyUrl, 'cover' => $storyCover, 'title' => $storyTitle,
            ]
        );
    }

    public function getStoryByState(?int $state = null): ?Truyenchon
    {
        return Truyenchon::where(['state' => $state])->first();
    }

    public function firstOrCreateChapter(string $storyUrl, string $chapterUrl, string $chapter)
    {
        return TruyenchonChapter::firstOrCreate(
            [
                'storyUrl' => $storyUrl, 'chapterUrl' => $chapterUrl, 'chapter' => $chapter,
            ]
        );
    }

    /**
     * @param string $url
     *
     * @return TruyenchonChapter|null
     */
    public function getChapterByUrl(string $url): ?TruyenchonChapter
    {
        return TruyenchonChapter::where(['chapterUrl' => $url])->first();
    }
}
