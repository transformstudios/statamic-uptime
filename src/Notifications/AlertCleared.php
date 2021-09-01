<?php

namespace TransformStudios\Uptime\Notifications;

use Illuminate\Support\Carbon;
use Statamic\Support\Arr;

class AlertCleared extends AbstractAlert
{
    protected string $event = 'alert_cleared';
    protected string $subject = 'Monitor Alert: Alert Cleared';

    public function toArray(): array
    {
        $startedAt = Carbon::parse(Arr::get($this->alert, 'downtime.started_at'));
        $endedAt = Carbon::parse(Arr::get($this->alert, 'downtime.ended_at'));

        return array_merge(
            parent::toArray(),
            [
                'duration' => $endedAt->diffForHumans($startedAt, Carbon::DIFF_ABSOLUTE, false, 3),
            ]
        );
    }
}
