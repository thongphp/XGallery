<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models\Jav;

use App\Models\Traits\HasCover;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Class JavMovieModel
 * @property int $id
 * @property string $name
 * @property string $cover
 * @property $sales_date
 * @property $release_date
 * @property string $content_id
 * @property string $dvd_id
 * @property string $description
 * @property int $time
 * @property string $director
 * @property string $studio
 * @property string $label
 * @property string $channel
 * @property string $sample
 * @property int $is_downloadable
 * @package App\Models\Jav
 */
class JavMovieModel extends Model
{
    use HasCover;

    protected $table = 'jav_movies';

    protected $fillable = [
        'name',
        'cover',
        'sales_date',
        'release_date',
        'content_id',
        'dvd_id',
        'description',
        'time',
        'director',
        'studio',
        'label',
        'channel',
        'series',
        'gallery',
        'sample',
        'is_downloadable',
    ];

    /**
     * @return BelongsToMany
     */
    public function idols(): BelongsToMany
    {
        return $this->belongsToMany(JavIdolModel::class, 'jav_idols_xref', 'movie_id', 'idol_id');
    }

    /**
     * @return BelongsToMany
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(JavGenreModel::class, 'jav_genres_xref', 'movie_id', 'genre_id');
    }
}
