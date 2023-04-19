<?php

namespace TransformStudios\Uptime;

use Statamic\Providers\AddonServiceProvider;
use TransformStudios\Uptime\Fieldtypes\Tag;

class ServiceProvider extends AddonServiceProvider
{
    protected $routes = [
        'actions' => __DIR__.'/../routes/actions.php',
    ];

    protected $fieldtypes = [
        Tag::class,
    ];

    public function bootAddon()
    {
        // needed for testing but not production
        if (app()->environment('testing')) {
            $this->loadViewsFrom(
                __DIR__.'/../resources/views',
                'uptime'
            );
        }
    }
}
