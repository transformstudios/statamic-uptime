<?php

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Support\Facades\Route;
use TransformStudios\Uptime\Http\Controllers\WebhookController;

Route::post('webhook', [WebhookController::class, '__invoke'])
    ->withoutMiddleware(VerifyCsrfToken::class)
    ->name('uptime.webhook');
