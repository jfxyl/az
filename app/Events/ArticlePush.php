<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class ArticlePush implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $article;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($article)
    {
        $this->article = $article->toArray();
        dump($this->article);
    }

    public function broadcastAs()
    {
        return 'EventArticlePush';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return ['ChannelArticlePush'];
    }

    // public function broadcastOn()
    // {
    //     return new PrivateChannel('channel-name');
    //     // return new PrivateChannel('channel-name');
    // }
}
