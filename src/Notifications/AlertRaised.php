<?php

namespace TransformStudios\Uptime\Notifications;

use Statamic\Support\Arr;

class AlertRaised extends AbstractAlert
{
    protected string $event = 'alert_raised';
    protected string $subject = 'Monitor Alert: Error Detected';

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'locations' => implode(',', Arr::get($this->alert, 'locations', [])),
            ]
        );
    }
}
