<?php

namespace App\Events;

use App\Events\Traits\ActivityEvent;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

abstract class UserActivity implements ActivityEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected ?Authenticatable $currentUser;

    protected string $actorTable = 'oauths';
    protected string $action = '';
    protected ?string $objectId = null;
    protected ?string $objectTable = null;
    protected $object;

    /**
     * Create a new event instance.
     * @param  string  $action
     */
    public function __construct(string $action, $object)
    {
        $this->currentUser = \Auth::user();
        $this->action = $action;
        $this->object = $object;
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

    public function getActorTable(): ?string
    {
        return $this->actorTable;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function getObjectId(): ?string
    {
        return $this->objectId;
    }

    public function getObjectTable(): ?string
    {
        return $this->objectTable;
    }

    public function getExtra(): array
    {
        return [];
    }

    public function getText(): string
    {
        return '%s have just %s %s';
    }

    public function translate(): string
    {
        return sprintf(
            $this->getText(),
            $this->currentUser->user['name'],
            $this->getAction(),
            (string) $this->object
        );
    }
}
