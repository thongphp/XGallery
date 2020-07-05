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
use App\Models\Jav\JavIdolModel;
use App\Models\Jav\JavMovieModel;
use App\Models\Jav\JavMovieXrefModel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Class UpdateIdols
 * @package App\Jobs\Jav
 */
class UpdateIdols implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    use HasJob;

    private JavMovieModel $movie;

    /**
     * @var array Array of idol names
     */
    private array     $idols;

    /**
     * UpdateJavIdols constructor.
     * @param  JavMovieModel  $movie
     * @param  array  $idols
     */
    public function __construct(JavMovieModel $movie, array $idols)
    {
        $this->movie = $movie;
        $this->idols = $idols;
        $this->onQueue(Queues::QUEUE_JAV);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // Process all actresses in this movie
        foreach ($this->idols as $actress) {
            $model = JavIdolModel::firstOrCreate(
                ['name' => $actress],
                ['name' => $actress, 'reference_url' => $this->movie->id]
            );
            JavMovieXrefModel::firstOrCreate([
                'xref_id' => $model->id, 'xref_type' => JavMovieXrefModel::XREF_TYPE_IDOL, 'movie_id' => $this->movie->id
            ]);
        }
    }
}
