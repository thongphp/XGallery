<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models\Jav;

use App\Facades\UserActivity;
use App\Models\DownloadableInterface;
use App\Models\JavDownload;
use App\Models\Traits\HasCover;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Auth;
use JustBetter\PaginationWithHavings\PaginationWithHavings;

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
class JavMovie extends Model implements DownloadableInterface
{
    use HasCover, PaginationWithHavings;

    public const DVD_ID = 'dvd_id';

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
        return $this->belongsToMany(JavIdol::class, 'jav_idols_xref', 'movie_id', 'idol_id');
    }

    /**
     * @return BelongsToMany
     */
    public function genres(): BelongsToMany
    {
        return $this->belongsToMany(JavGenre::class, 'jav_genres_xref', 'movie_id', 'genre_id');
    }

    /**
     * @return bool
     */
    public function isDownloading(): bool
    {
        return null !== JavDownload::where([JavDownload::ITEM_NUMBER => $this->dvd_id])->first();
    }

    /**
     * @param User|null $author
     *
     * @return void
     */
    public function startDownload(?User $author = null): void
    {
        $model = app(JavDownload::class);
        $model->{JavDownload::ITEM_NUMBER} = $this->{self::DVD_ID};
        $model->save();

        UserActivity::notify(
            '%s request %s movie',
            $author ?? Auth::user(),
            'download',
            [
                \App\Models\Core\UserActivity::OBJECT_ID => $this->id,
                \App\Models\Core\UserActivity::OBJECT_TABLE => $this->getTable(),
                \App\Models\Core\UserActivity::EXTRA => [
                    'title' => $this->name,
                    'fields' => [
                        'Title' => $this->name,
                        'DVD-ID' => $this->dvd_id,
                        'Director' => $this->director,
                        'Studio' => $this->studio,
                    ],
                ],
            ]
        );
    }
}
