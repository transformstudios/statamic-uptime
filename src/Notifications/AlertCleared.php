<?php

namespace TransformStudios\Uptime\Notifications;

class AlertCleared extends AbstractAlert
{
    protected string $event = 'alert_cleared';
    protected string $subject = 'Monitor Alert: Alert Cleared';
}
