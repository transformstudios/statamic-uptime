<?php

namespace TransformStudios\Uptime\Notifications;

class AlertRaised extends AbstractAlert
{
    protected string $event = 'alert_raised';
    protected string $subject = 'Monitor Alert: Error Detected';
}
