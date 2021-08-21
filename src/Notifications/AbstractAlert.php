<?php

namespace TransformStudios\Uptime\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Statamic\Support\Arr;

abstract class AbstractAlert extends Notification
{
    use Queueable;

    protected string $event;
    protected string $subject;

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
            'event' => $this->event,
            'date' => Arr::get($this->alert, 'date'),
            'locations' =>Arr::get($this->alert, 'locations'),
            'output' => Arr::get($this->alert, 'alert.output'),
            'subject' => $this->subject,
            'test' => Arr::get($this->alert, 'name'),
            'type' => Arr::get($this->alert, 'monitoring_service_type'),
        ];
    }
}
