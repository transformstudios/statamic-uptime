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
    public function toArray(): array
    {
        return [
            'event' => $this->event,
            'date' => Arr::get($this->alert, 'date'),
            'is_up' => Arr::get($this->alert, 'alert.is_up'),
            'output' => Arr::get($this->alert, 'alert.short_output'),
            'subject' => $this->subject,
            'test' => Arr::get($this->alert, 'service.name'),
            'type' => Arr::get($this->alert, 'service.monitoring_service_type'),
        ];
    }
}
