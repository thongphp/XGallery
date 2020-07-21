<?php
/**
 * Copyright (c) 2020 JOOservices Ltd
 * @author Viet Vu <jooservices@gmail.com>
 * @package XGallery
 * @license GPL
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 */

namespace App\Models\Truyenchon;

use App\Database\Mongodb;
use App\Models\Traits\HasCover;
use App\Models\Traits\HasUrl;
use Illuminate\Database\Eloquent\Collection;
use Jenssegers\Mongodb\Relations\HasMany;

/**
 * Class TruyenchonModel
 * @package App\Models\Truyenchon
 *
 * @property string $url
 * @property array $images
 * @property string $title
 * @property string $description
 * @property int $state
 * @property null|Collection $chapters
 */
class TruyenchonModel extends Mongodb
{
    use HasUrl;
    use HasCover;

    const STATE_PROCESSED = 1;
    public const KEY_URL = 'url';

    public $collection = 'truyenchon';

    protected $fillable = ['url', 'cover', 'title'];

    /**
     * Get chapters of this comic
     * Example:
     * $this->chapters // Get array of associated FlickPhotoModel
     * $this->chapters() // Get query builder
     *
     * @return HasMany
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(TruyenchonChapterModel::class, TruyenchonChapterModel::KEY_STORY_URL, self::KEY_URL);
    }
}
