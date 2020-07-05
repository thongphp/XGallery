<?php

namespace App\Jobs\Jav;

use App\Crawlers\Crawler\XCityProfile;
use App\Jobs\Middleware\RateLimited;
use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\JavIdols;
use App\Models\JavMovieModel;
use App\Models\JavMoviesXref;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Process to get XCity video detail
 * @package App\Jobs
 */
class XCityVideo implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    protected array $item;

    /**
     * Create a new job instance.
     *
     * @param  array  $item
     */
    public function __construct(array $item)
    {
        $this->item = $item;
        $this->onQueue(Queues::QUEUE_JAV);
    }

    /**
     * @return RateLimited[]
     */
    public function middleware()
    {
        return [new RateLimited('xcity')];
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$itemDetail = app(\App\Crawlers\Crawler\XCityVideo::class)->getItemDetail($this->item['url'])) {
            $this->release(900); // 15 minutes
            return;
        }

        $model = app(JavMovieModel::class);
        // To store in JavMovieModel we use item_number as unique
        if (!$movie = $model->where(['item_number' => $itemDetail->item_number])->first()) {
            // Not found than create new model
            $movie = app(JavMovieModel::class);
            Log::stack(['jav'])->info('Saving new video '.$itemDetail->item_number);
        }

        $movie->name = $itemDetail->title;
        $movie->reference_url = $itemDetail->url;
        $movie->gallery = json_encode($itemDetail->gallery);
        $movie->sales_date = isset($itemDetail->sales_date) ? $itemDetail->sales_date : null;
        $movie->label = $itemDetail->label;
        // @TODO Maker
        $movie->series = $itemDetail->series;
        $movie->director = $itemDetail->director;
        $movie->item_number = $itemDetail->item_number;
        $movie->time = $itemDetail->time;
        $movie->release_date = $itemDetail->release_date ?? null;
        $movie->description = isset($itemDetail->description) ? $itemDetail->description : null;

        $movie->save();

        /**
         * Process idol directly here
         */

        $crawler = app(XCityProfile::class);
        foreach ($itemDetail->actresses as $actress) {
            $model = app(JavIdols::class);
            /**
             * Can not get item detail. Maybe something wrong on XCity because we are landed on XCity by movie already
             */
            if (!$actressDetail = $crawler->getItemDetail($actress[0])) {
                // Check by movie id and insert xref if found already
                if ($item = $model->where(['reference_url' => $movie->id])->first()) {
                    $this->insertXRef($item, $movie);
                    continue;
                }
                // Create new model
                $model->name = $actress[1];
                $model->reference_url = $movie->id;
                $model->save();
                $this->insertXRef($model, $movie);
                unset($model);
                continue;
            }

            /**
             * Got detail
             */
            if ($item = $model->where(['reference_url' => $actressDetail->url])->first()) {
                // Found this model already than insert Xref only
                $this->insertXRef($item, $movie);
                unset($model);
                continue;
            }

            $model->name = $actressDetail->name;
            $model->reference_url = $actressDetail->url;
            $model->cover = $actressDetail->cover;
            $model->favorite = $actressDetail->favorite ?? null;
            $model->birthday = $actressDetail->birthday ?? null;
            $model->blood_type = $actressDetail->blood_type ?? null;
            $model->city = $actressDetail->city ?? null;
            $model->height = $actressDetail->height ?? null;
            $model->breast = $actressDetail->breast ?? null;
            $model->waist = $actressDetail->waist ?? null;
            $model->hips = $actressDetail->hips ?? null;

            $model->save();
            $this->insertXRef($model, $movie);
        }

        UpdateGenres::dispatch($movie, $itemDetail->genres);
    }

    /**
     * @param  JavIdols  $idolModel
     * @param  JavMovieModel  $movie
     */
    private function insertXRef(JavIdols $idolModel, JavMovieModel $movie)
    {
        $model = app(JavMoviesXref::class);
        $xref = ['xref_id' => $idolModel->id, 'xref_type' => 'idol', 'movie_id' => $movie->id];
        if ($model->where($xref)->first()) {
            return;
        }

        $model->insert($xref);
    }
}
