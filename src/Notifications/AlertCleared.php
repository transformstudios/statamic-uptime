<?php

namespace TransformStudios\Uptime\Notifications;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Statamic\Support\Arr;

class AlertCleared extends AbstractAlert
{
    public function __construct(array $payload, Collection $users)
    {
        parent::__construct(
            subject: 'Monitor Alert: Alert Cleared',
            payload: $payload,
            template: 'alert_cleared',
            users: $users
        );
    }

    protected function data(array $payload, array $additionalData = []): array
    {
        $startedAt = Carbon::parse(Arr::get($payload, 'downtime.started_at'));
        $endedAt = Carbon::parse(Arr::get($payload, 'downtime.ended_at'));

        return parent::data(
            $payload,
            ['duration' => $endedAt->diffForHumans($startedAt, Carbon::DIFF_ABSOLUTE, false, 3)]
        );
    }
}
