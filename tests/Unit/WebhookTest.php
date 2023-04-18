<?php

namespace TransformStudios\Uptime\Tests\Unit;

use Illuminate\Support\Facades\Notification;
use Statamic\Entries\Collection;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Entry;
use Statamic\Facades\User;
use TransformStudios\Uptime\Notifications\AlertCleared;
use TransformStudios\Uptime\Notifications\AlertRaised;
use TransformStudios\Uptime\Tests\TestCase;

class WebhookTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /** @test */
    public function ignores_unhandled_events()
    {
        $this->post(route('statamic.uptime.webhook'), [])
            ->assertNoContent();
    }

    /** @test */
    public function ignores_alert_cleared_if_no_tag()
    {
        $data = [
            'data' => [],
            'event' => 'alert_cleared',
        ];

        $this->post(route('statamic.uptime.webhook'), $data)
            ->assertNoContent();
    }

    /** @test */
    public function ignores_alert_cleared_if_tag_not_used()
    {
        $data = [
            'data' => [
                'service' => [
                    'tags' => [
                        'foo',
                    ],
                ],
            ],
            'event' => 'alert_cleared',
        ];

        $this->post(route('statamic.uptime.webhook'), $data)
            ->assertNoContent();
    }

    /** @test */
    public function sends_alert_cleared_notification()
    {
        $data = [
            'data' => [
                'service' => [
                    'id' => 123,
                    'tags' => [
                        'foo',
                    ],
                ],
            ],
            'event' => 'alert_cleared',
        ];

        $user = User::make()
            ->email('foo@bar.com')
            ->save();

        Blueprint::setDirectory(__DIR__.'/../__fixtures__/blueprints/');

        (new Collection)->handle('sites')->save();

        Entry::make()
            ->collection('sites')
            ->in('default')
            ->set('title', 'Test Site')
            ->set('uptime_tag', 'foo')
            ->set('users', [$user->id()])
            ->save();

        Notification::fake();

        $this->post(route('statamic.uptime.webhook'), $data)
            ->assertOk();

        Notification::assertTimesSent(1, AlertCleared::class);
    }

    /** @test */
    public function sends_alert_raised_notification()
    {
        $data = [
            'data' => [
                'service' => [
                    'tags' => [
                        'foo',
                    ],
                ],
            ],
            'event' => 'alert_raised',
        ];

        $user = User::make()
            ->email('foo@bar.com')
            ->save();

        Blueprint::setDirectory(__DIR__.'/../__fixtures__/blueprints/');

        (new Collection)->handle('sites')->save();

        Entry::make()
            ->collection('sites')
            ->in('default')
            ->set('title', 'Test Site')
            ->set('uptime_tag', 'foo')
            ->set('users', [$user->id()])
            ->save();

        Notification::fake();

        $this->post(route('statamic.uptime.webhook'), $data)
            ->assertOk();

        Notification::assertTimesSent(1, AlertRaised::class);
    }
}
