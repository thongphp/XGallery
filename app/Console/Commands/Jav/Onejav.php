<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Console\Commands\Jav;

use App\Console\BaseCommand;
use App\Models\Jav\JavMovie;
use App\Traits\Jav\HasXref;
use Exception;
use Illuminate\Support\Collection;

/**
 * Class Onejav
 * @description This command only use for basic movie information WITH download link and genre. Idol with name only
 * This command will not trigger any queues
 * @package App\Console\Commands\Jav
 */
final class Onejav extends BaseCommand
{
    use HasXref;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jav:onejav {task=fully} {--url=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetching data from OnejavModel';

    /**
     * @return bool
     */
    protected function daily(): bool
    {
        $crawler = app(\App\Crawlers\Crawler\Onejav::class);
        return $this->itemsProcess($crawler->getDaily());
    }

    /**
     * Process to get all OneJav data
     * @return bool
     * @throws Exception
     */
    protected function fully(): bool
    {
        if (!$endpoint = $this->getEndpoint('Onejav')) {
            return false;
        }

        $items = app(\App\Crawlers\Crawler\Onejav::class)->getItems($endpoint->url.'?page='.$endpoint->page);

        if ($items->isEmpty()) {
            $endpoint->fail()->save();
            return false;
        }

        $this->itemsProcess($items);
        $endpoint->succeed()->save();

        return true;
    }

    /**
     * @param  Collection  $items
     * @return bool
     */
    private function itemsProcess(Collection $items)
    {
        if ($items->isEmpty()) {
            return true;
        }

        $this->progressBarInit($items->count());
        $items->each(function ($item) {
            $attributes = $item->getAttributes();
            $item = \App\Models\Jav\Onejav::updateOrCreate(['url' => $attributes['url']], $attributes);
            $movie = JavMovie::updateOrCreate(
                ['dvd_id' => $item->title],
                [
                    'release_date' => $item->date,
                    'is_downloadable' => true,
                    'description' => $item->description,
                    'cover' => $item->cover
                ]
            );

            $this->updateGenres($item->tags, $movie);
            $this->updateIdols($item->actresses, $movie);

            $this->progressBarSetInfo($item->url);
            $this->progressBarSetStatus('COMPLETED');
            $this->progressBar->advance();
        });

        $this->progressBarFinished();

        return true;
    }
}
