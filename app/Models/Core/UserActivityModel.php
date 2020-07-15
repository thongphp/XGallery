<?php

namespace App\Models\Core;

use App\Models\User;
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
 * @package App\Models\Core
 */
class UserActivityModel extends Model
{
    use Notifiable;

    protected $table = 'user_activities';
    protected $fillable = ['actor_id', 'actor_table', 'action', 'object_id', 'object_table', 'text', 'extra'];

    protected $casts = [
        'extra' => 'object'
    ];

    /**
     * Route notifications for the Slack channel.
     *
     * @SuppressWarnings("unused")
     *
     * @param  Notification  $notification
     *
     * @return mixed
     */
    public function routeNotificationForSlack($notification)
    {
        return config('services.slack.webhook_url');
    }

    public function trans()
    {
        $actor = $this->actor_id ? User::find($this->actor_id)->name : 'Guest';
        $action = $this->action;

        return sprintf($this->text, $actor, $action);
    }
}
