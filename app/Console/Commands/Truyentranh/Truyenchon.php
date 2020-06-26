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
use Exception;
use Illuminate\Support\Collection;

/**
 * Class Truyenchon
 * @package App\Console\Commands\Truyentranh
 */
final class Truyenchon extends BaseCrawlerCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'truyentranh:truyenchon {task=fully} {--url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching stories from http://truyenchon.com/';

    /**
     * @return bool
     * @throws Exception
     */
    protected function fully(): bool
    {
        if (!$pages = $this->getIndexLinks()) {
            return false;
        }

        $this->progressBarInit($pages->count());

        // Process all pages
        $pages->each(function ($stories) {
            /**
             * @var Collection $stories
             */
            if ($stories->isEmpty()) {
                $this->progressBar->advance();
                return;
            }
            $this->progressBarSetSteps($stories->count());

            // Process items on page
            $stories->each(function ($story) {
                \App\Models\Truyenchon::firstOrCreate([
                    'url' => $story['url'], 'cover' => $story['cover'], 'title' => $story['title']
                ]);
                $this->progressBarAdvanceStep();
            });
            $this->progressBar->advance();
        });

        return true;
    }
}
