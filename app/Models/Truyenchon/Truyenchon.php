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
use App\Facades\UserActivity;
use App\Jobs\Truyenchon\TruyenchonChapterDownload;
use App\Models\Traits\HasCover;
use App\Models\Traits\HasUrl;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Mongodb\Relations\HasMany;

/**
 * Class TruyenchonModel
 * @package App\Models\Truyenchon
 *
 * @property string $_id
 * @property string $url
 * @property array $images
 * @property string $title
 * @property string $description
 * @property int $state
 * @property null|Collection $chapters
 */
class Truyenchon extends Mongodb
{
    use HasUrl;
    use HasCover;

    public const ID = '_id';
    public const URL = 'url';
    public const TITLE = 'title';
    public const COVER = 'cover';

    public $collection = 'truyenchon';

    protected $fillable = [self::URL, self::COVER, self::TITLE];

    /**
     * Get chapters of this comic
     * Example:
     * $this->chapters // Get array of associated TruyenchonChapter
     * $this->chapters() // Get query builder
     *
     * @return HasMany
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(TruyenchonChapter::class, TruyenchonChapter::STORY_URL, self::URL);
    }

    public function isDownloading(): bool
    {
        return (bool) TruyenchonDownload::where(
            [
                TruyenchonDownload::STORY_ID => $this->_id,
                TruyenchonDownload::USER_ID => Auth::id(),
            ]
        )->first();
    }

    public function download(?string $userId): void
    {
        $user = !$userId ? Auth::user() : User::find($userId);

        foreach ($this->chapters as $chapter) {
            TruyenchonChapterDownload::dispatch($chapter, $user->id ?? null);
        }
    }
}
