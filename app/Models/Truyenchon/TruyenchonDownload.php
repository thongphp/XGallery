<?php

namespace App\Models\Truyenchon;

use App\Database\Mongodb;
use App\Jobs\Truyenchon\TruyenchonStoryDownload;
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

    public function download(): void
    {
        $this->{self::STATE} = self::STATE_PROCESS;
        $this->save();

        TruyenchonStoryDownload::dispatch($this->story, $this->user_id);
    }
}
