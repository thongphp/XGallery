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
use App\Models\TruyenchonModel;
use App\Repositories\TruyenchonRepository;
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
        $repository = app(TruyenchonRepository::class);
        if (!$story = $repository->getStoryByState()) {
            return true;
        }

        $story->state = TruyenchonModel::STATE_PROCESSED;
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
            $repository->firstOrCreateChapter($story['url'], $chapter['url'], $chapter['chapter']);
            Chapters::dispatch($chapter['url']);
        }

        return true;
    }
}
