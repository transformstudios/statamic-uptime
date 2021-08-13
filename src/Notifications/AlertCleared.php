<?php

namespace TransformStudios\Uptime\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use TransformStudios\Front\Notifications\Channel\Message;

class AlertCleared extends Notification
{
    use Queueable;

    public function __construct(private $alert = [])
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['front'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \TransformStudios\Front\Notifications\Channel\Message
     */
    public function toFront($notifiable)
    {
        return new Message($notifiable);
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->alert;
    }
}
