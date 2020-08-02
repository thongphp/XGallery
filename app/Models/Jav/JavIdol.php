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
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use JustBetter\PaginationWithHavings\PaginationWithHavings;

/**
 * Class JavIdolModel
 * @package App\Models\Jav
 *
 * @property string $id
 * @property string $name
 */
class JavIdol extends Model
{
    use HasCover, PaginationWithHavings;

    protected $fillable = [
        'name', 'alias', 'birthday', 'blood_type', 'city', 'height', 'breast', 'waist', 'hips', 'cover', 'favorite'
    ];

    protected $casts = [
        'birthday' => 'date'
    ];

    protected $table = 'jav_idols';

    /**
     * @return BelongsToMany
     */
    public function movies(): BelongsToMany
    {
        return $this->belongsToMany(JavMovie::class, 'jav_idols_xref', 'idol_id', 'movie_id');
    }

    /**
     * @return int|null
     */
    public function getAge(): ?int
    {
        return $this->birthday ? $this->birthday->diffInYears(Carbon::now()) : null;
    }

    /**
     * @return string|null
     */
    public function getBirthday(): ?string
    {
        return $this->birthday ? $this->birthday->format('Y-m-d') : null;
    }
}
