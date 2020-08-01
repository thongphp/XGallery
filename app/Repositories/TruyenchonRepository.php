<?php

namespace App\Repositories;

use App\Models\Truyenchon\Truyenchon;
use App\Models\Truyenchon\TruyenchonChapter;

/**
 * Class TruyenchonRepository
 * @package App\Repositories
 */
class TruyenchonRepository extends BaseRepository
{
    public function __construct(Truyenchon $model)
    {
        parent::__construct($model);
    }

    public function getItems(array $filter = [])
    {
        if (isset($filter['keyword']) && !empty($filter['keyword'])) {
            $this->builder->where('title', 'LIKE', '%'.$filter['keyword'].'%');
        }

        return parent::getItems($filter);
    }

    public function firstOrCreate(string $storyUrl, string $storyCover, string $storyTitle)
    {
        return Truyenchon::firstOrCreate([
            'url' => $storyUrl, 'cover' => $storyCover, 'title' => $storyTitle
        ]);
    }

    public function getStoryByState(?int $state = null): ?Truyenchon
    {
        return Truyenchon::where(['state' => $state])->first();
    }

    public function firstOrCreateChapter(string $storyUrl, string $chapterUrl, string $chapter)
    {
        return TruyenchonChapter::firstOrCreate([
            'storyUrl' => $storyUrl, 'chapterUrl' => $chapterUrl, 'chapter' => $chapter
        ]);
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
