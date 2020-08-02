<?php

namespace App\Models\Core;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;

/**
 * Class UserActivityModel
 * @property int $actor_id
 * @property int $actor_table
 * @property int $action;
 * @property int $object_id;
 * @property int $object_table;
 * @property int $text;
 * @property int $extra;
 * @property Carbon $created_at;
 * @package App\Models\Core
 */
class UserActivity extends Model
{
    use Notifiable;

    public const CREATED_AT = 'created_at';
    public const ACTOR_ID = 'actor_id';
    public const ACTOR_TABLE = 'actor_table';
    public const ACTION = 'action';
    public const OBJECT_ID = 'object_id';
    public const OBJECT_TABLE = 'object_table';
    public const EXTRA = 'extra';
    public const TEXT = 'extra';

    protected $table = 'user_activities';
    protected $fillable = [
        self::ACTOR_ID, self::ACTOR_TABLE, self::ACTION, self::OBJECT_ID, self::OBJECT_TABLE,
        self::TEXT, self::EXTRA,
    ];

    protected $casts = [
        'extra' => 'object',
    ];

    /**
     * Route notifications for the Slack channel.
     *
     * @SuppressWarnings("unused")
     *
     * @param Notification $notification
     *
     * @return mixed
     */
    public function routeNotificationForSlack($notification)
    {
        return config('services.slack.webhook_url');
    }

    public function trans(): string
    {
        $actor = $this->actor_id ? User::find($this->actor_id)->name : 'Guest';
        $action = $this->action;

        return sprintf($this->text, $actor, $action);
    }
}
