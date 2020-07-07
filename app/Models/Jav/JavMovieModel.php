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
use Illuminate\Support\Facades\DB;

/**
 * Class JavMovieModel
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
 * @property string $reference_url
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
        'reference_url',
    ];

    public function search(array $fields, string $keyword)
    {
        $this->where(function ($query) use ($fields, $keyword) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', '%'.$keyword.'%');
            }
        });

        return $this;
    }

    public function idols()
    {
        $query = DB::table('jav_idols AS idol');
        $query
            ->leftJoin('jav_movies_xrefs as xref', 'xref.xref_id', '=', 'idol.id')
            ->where('xref.xref_type', '=', 'idol')
            ->where('xref.movie_id', '=', $this->id)
            ->select('idol.*');

        return $query->get();
    }

    public function genres()
    {
        $query = DB::table('jav_genres AS idol');
        $query
            ->leftJoin('jav_movies_xrefs as xref', 'xref.xref_id', '=', 'idol.id')
            ->where('xref.xref_type', '=', 'genre')
            ->where('xref.movie_id', '=', $this->id)
            ->select('idol.*');

        return $query->get();
    }
}
