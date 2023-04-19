<?php

namespace TransformStudios\Uptime\Notifications;

use Illuminate\Support\Collection;
use Statamic\Support\Arr;
use TransformStudios\Front\Notifications\BaseNotification;

abstract class AbstractAlert extends BaseNotification
{
    public function __construct(string $subject, array $payload, string $template, Collection $users)
    {
        parent::__construct(
            Arr::get($payload, 'service.id'),
            $subject,
            view("uptime::$template", $this->data($payload))->render(),
            $users,
        );
    }

    protected function data(array $payload, array $additionalData = []): array
    {
        return array_merge(
            [
                'date' => Arr::get($payload, 'date'),
                'is_up' => Arr::get($payload, 'alert.is_up'),
                'output' => Arr::get($payload, 'alert.short_output'),
                'test' => Arr::get($payload, 'service.name'),
                'type' => Arr::get($payload, 'service.monitoring_service_type'),
            ],
            $additionalData,
        );
    }
}
