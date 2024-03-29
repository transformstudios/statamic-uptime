<?php

namespace TransformStudios\Uptime\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;
use Statamic\Support\Arr;
use TransformStudios\Uptime\Notifications\AlertCleared;
use TransformStudios\Uptime\Notifications\AlertRaised;

class WebhookController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function __invoke(Request $request)
    {
        $method = 'handle'.Str::studly(str_replace('.', '_', $request->input('event')));

        if (method_exists($this, $method)) {
            return $this->{$method}($request->input('data'));
        }

        return response()->noContent();
    }

    private function handleAlertCleared(array $payload)
    {
        return $this->handleAlert(AlertCleared::class, $payload);
    }

    private function handleAlertRaised(array $payload)
    {
        return $this->handleAlert(AlertRaised::class, $payload);
    }

    private function handleAlert(string $notificationClass, array $payload)
    {
        if (! $tag = Arr::get($payload, 'service.tags.0')) {
            return response()->noContent();
        }

        if (! $site = Entry::query()->where('collection', 'sites')->where('published', true)->where('uptime_tag', $tag)->first()) {
            return response()->noContent();
        }

        if ($site->users->isEmpty()) {
            return response()->noContent();
        }

        (new AnonymousNotifiable)->notify(new $notificationClass($payload, $site->users));

        return response('Webhook handled');
    }
}
