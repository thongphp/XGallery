<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Jobs\Jav;

use App\Jobs\Queues;
use App\Jobs\Traits\HasJob;
use App\Models\Jav\JavGenreModel;
use App\Models\Jav\JavMovieModel;
use App\Models\Jav\JavMovieXrefModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class UpdateGenres
 * @package App\Jobs\Jav
 */
class UpdateGenres implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private JavMovieModel $movie;
    private array     $genres;

    /**
     * UpdateJavGenres constructor.
     * @param  JavMovieModel  $movie
     * @param  array  $genres
     */
    public function __construct(JavMovieModel $movie, array $genres)
    {
        $this->movie = $movie;
        $this->genres = $genres;
        $this->onQueue(Queues::QUEUE_JAV);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->genres as $tag) {
            $model = JavGenreModel::firstOrCreate(['name' => $tag], ['name' => $tag]);
            JavMovieXrefModel::firstOrCreate([
                'xref_id' => $model->id,
                'xref_type' => JavMovieXrefModel::XREF_TYPE_GENRE,
                'movie_id' => $this->movie->id
            ]);
        }
    }
}
