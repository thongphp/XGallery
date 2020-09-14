<?php

namespace App\Models\Truyenchon;

use App\Database\Mongodb;
use App\Facades\UserActivity;
use App\Jobs\Truyenchon\TruyenchonStoryDownload;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Mongodb\Relations\HasOne;

/**
 * Class TruyenchonDownload
 * @package App\Models\Truyenchon
 *
 * @property string $_id
 * @property string $story_id
 * @property string $user_id
 * @property int $state
 *
 * @property Truyenchon $story
 */
class TruyenchonDownload extends Mongodb
{
    public const STORY_ID = 'story_id';
    public const USER_ID = 'user_id';
    public const STATE = 'state';

    public const STATE_PROCESS = 1;

    public $collection = 'truyenchon_download';

    protected $fillable = [self::STORY_ID, self::USER_ID];

    public function story(): HasOne
    {
        return $this->hasOne(Truyenchon::class, Truyenchon::ID, self::STORY_ID);
    }

    public function isProcessing(): bool
    {
        return $this->state === self::STATE_PROCESS;
    }

    /**
     * @param false $isReDownload
     */
    public function download(bool $isReDownload = false): void
    {
        $this->{self::STATE} = self::STATE_PROCESS;
        $this->save();

        $story = $this->story;

        UserActivity::notify(
            '%s request %s story',
            Auth::user(),
            $isReDownload === true ? 're-download' : 'download',
            [
                \App\Models\Core\UserActivity::OBJECT_ID => $story->{Truyenchon::ID},
                \App\Models\Core\UserActivity::OBJECT_TABLE => $story->getTable(),
                \App\Models\Core\UserActivity::EXTRA => [
                    'title' => $story->{Truyenchon::TITLE},
                    'fields' => [
                        'ID' => $story->{Truyenchon::ID},
                        'Title' => $story->{Truyenchon::TITLE},
                    ],
                ],
            ]
        );

        TruyenchonStoryDownload::dispatch($this->story, $this->user_id);
    }
}
