<?php

namespace App\Repositories;

use App\Models\Truyenchon\TruyenchonChapterModel;
use App\Models\Truyenchon\TruyenchonModel;

/**
 * Class TruyenchonRepository
 * @package App\Repositories
 */
class TruyenchonRepository extends BaseRepository
{
    public function __construct(TruyenchonModel $model)
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
        return TruyenchonModel::firstOrCreate([
            'url' => $storyUrl, 'cover' => $storyCover, 'title' => $storyTitle
        ]);
    }

    public function getStoryByState(?int $state = null): ?TruyenchonModel
    {
        return TruyenchonModel::where(['state' => $state])->first();
    }

    public function firstOrCreateChapter(string $storyUrl, string $chapterUrl, string $chapter)
    {
        return TruyenchonChapterModel::firstOrCreate([
            'storyUrl' => $storyUrl, 'chapterUrl' => $chapterUrl, 'chapter' => $chapter
        ]);
    }

    /**
     * @param string $url
     *
     * @return TruyenchonChapterModel|null
     */
    public function getChapterByUrl(string $url): ?TruyenchonChapterModel
    {
        return TruyenchonChapterModel::where(['chapterUrl' => $url])->first();
    }
}
