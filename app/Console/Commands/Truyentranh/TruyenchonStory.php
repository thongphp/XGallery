<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console\Commands\Truyentranh;

use App\Console\BaseCrawlerCommand;
use App\Jobs\Truyenchon\Chapters;
use App\Models\TruyenchonChapter;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class TruyenchonStory
 * @package App\Console\Commands\Truyentranh
 */
final class TruyenchonStory extends BaseCrawlerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'truyentranh:truyenchonstory {task=fully}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching chapter of a story of Truyenchon';

    /**
     * @return bool
     * @throws Exception
     */
    protected function fully(): bool
    {
        // Get story is not completed
        /**
         * @var \App\Models\Truyenchon $story
         */
        if (!$story = \App\Models\Truyenchon::where(['state' => null])->first()) {
            return true;
        }

        $story->state = \App\Models\Truyenchon::STATE_PROCESSED;
        $story->save();

        $crawler = app(\App\Crawlers\Crawler\Truyenchon::class);

        /**
         * @var Collection $chapters
         */
        $chapters = $crawler->getItemChapters($story['url']);
        if ($chapters->isEmpty()) {
            return true;
        }

        $chapters = $chapters->map(function ($url) {
            return [
                'url' => $url,
                'chapter' => explode('/', $url)[5]
            ];
        })->toArray();

        foreach ($chapters as $chapter) {
            TruyenchonChapter::firstOrCreate([
                'storyUrl' => $story['url'], 'chapterUrl' => $chapter['url'], 'chapter' => $chapter['chapter']
            ]);
            Chapters::dispatch($chapter['url']);
        }

        return true;
    }
}
