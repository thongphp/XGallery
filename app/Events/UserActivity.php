<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserActivity
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    private ?\Illuminate\Contracts\Auth\Authenticatable $currentUser;
    private string $action;
    private $extra;

    /**
     * Create a new event instance.
     *extra
     */
    public function __construct(string $action, $extra)
    {
        $this->currentUser = \Auth::user();
        $this->action = $action;
        $this->extra = $extra;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    public function getActor()
    {
        return $this->currentUser;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getExtra()
    {
        return $this->extra;
    }
}
