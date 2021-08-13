<?php

use App\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use TransformStudios\Uptime\Http\Controllers\WebhookController;

Route::post('webhook', [WebhookController::class, '__invoke'])
    ->withoutMiddleware(VerifyCsrfToken::class)
    ->name('uptime.webhook');

Route::get('test', function(Request $request) {
    // abort(500);
    return response()->noContent();
})
->name('uptime.test');
