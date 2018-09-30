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
    protected $user;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($article,$user = null)
    {
        $article->content = str_limit($article->content,60,'...');
        $this->article = $article->toArray();
        if($user) $this->user = $user;
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
        if($this->user){
            return ['ChannelArticlePush_'.$this->user->id];
        }else{
            return ['ChannelArticlePush'];
        }
        
    }

    // public function broadcastOn()
    // {
    //     return new PrivateChannel('channel-name');
    //     // return new PrivateChannel('channel-name');
    // }
}
