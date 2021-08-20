<?php

namespace TransformStudios\Uptime\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Statamic\Support\Arr;

abstract class AbstractAlert extends Notification
{
    use Queueable;

    protected string $event;

    public function __construct(protected $alert = [])
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
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'email' => $notifiable->email(),
            'test' => Arr::get($this->alert, 'name'),
            'date' => Arr::get($this->alert, 'date'),
            'event' => $this->event,
        ];
    }
}
