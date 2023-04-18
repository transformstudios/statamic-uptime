<?php

namespace TransformStudios\Uptime\Notifications;

use Illuminate\Support\Collection;
use Statamic\Support\Arr;

class AlertRaised extends AbstractAlert
{
    protected string $event = 'alert_raised';

    protected string $subject = 'Monitor Alert: Error Detected';

    public function __construct(array $payload, Collection $users)
    {
        parent::__construct(
            subject: 'Monitor Alert: Error Detected',
            payload: $payload,
            template: 'alert_raised',
            users: $users
        );
    }

    protected function data(array $payload, array $additionalData = []): array
    {
        return parent::data(
            $payload,
            ['locations' => implode(',', Arr::get($payload, 'locations', []))]
        );
    }
}
