<?php

namespace App\Events;

use App\Models\Jav\JavMovie;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JavMovieCreated implements JavMovieEventInterface
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var JavMovie
     */
    private JavMovie $javMovie;

    /**
     * Create a new event instance.
     *
     * @param  JavMovie  $javMovie
     */
    public function __construct(JavMovie $javMovie)
    {
        $this->javMovie = $javMovie;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }

    public function getMovie(): JavMovie
    {
        return $this->javMovie;
    }
}
