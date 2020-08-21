<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console\Commands\Truyentranh;

use App\Console\BaseCommand;
use App\Jobs\Truyenchon\TruyenchonChapterGetImages;
use App\Models\Truyenchon\Truyenchon;
use App\Models\Truyenchon\TruyenchonChapter;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class TruyenchonStory
 * @package App\Console\Commands\Truyenchon
 */
final class TruyenchonStory extends BaseCommand
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
        $story = Truyenchon::orderBy('updated_at', 'asc')->first();
        $story->touch();
        $this->output->note('Working on ' . $story->title);
        $crawler = app(\App\Crawlers\Crawler\Truyenchon::class);

        /**
         * @var Collection $chapters
         */
        $chapters = $crawler->getChapters($story->url);
        if ($chapters->isEmpty()) {
            return true;
        }

        $chapters = $chapters->map(function ($url) {
            return [
                'chapterUrl' => $url,
                'chapter' => explode('/', $url)[5]
            ];
        })->toArray();

        $this->progressBarInit(count($chapters));
        $this->progressBarSetMessage('Chapters');

        foreach ($chapters as $chapter) {
            $model = TruyenchonChapter::firstOrCreate([
                TruyenchonChapter::STORY_URL => $story->url, TruyenchonChapter::CHAPTER_URL => $chapter['chapterUrl']
            ], $chapter);
            TruyenchonChapterGetImages::dispatch($model);
            $this->progressBarSetInfo($chapter['chapterUrl']);
            $this->progressBarSetStatus('QUEUED');
            $this->progressBar->advance();
        }

        return true;
    }
}
