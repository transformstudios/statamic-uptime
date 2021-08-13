<?php

namespace TransformStudios\Uptime\Tests\Unit;

use Illuminate\Support\Facades\Notification;
use Statamic\Entries\Collection;
use Statamic\Facades\Blueprint;
use Statamic\Facades\Entry;
use Statamic\Facades\File;
use Statamic\Facades\User;
use Statamic\Facades\YAML;
use TransformStudios\Uptime\Notifications\AlertCleared;
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
                    'tags' => [
                        'foo',
                    ],
                ],
            ],
            'event' => 'alert_cleared',
        ];

        $user = User::make()->email('foo@bar.com');

        $user->save();

        $blueprint = Blueprint::makeFromFields(YAML::file(__DIR__.'/../__fixtures__/blueprints/sites.yaml')->parse())
            ->setHandle('sites');
        $userBlueprint = Blueprint::makeFromFields(YAML::file(__DIR__.'/../__fixtures__/blueprints/user.yaml')->parse())
            ->setHandle('user');
        Blueprint::shouldReceive('in')->with('collections/sites')->andReturn(collect(['sites' => $blueprint]));
        Blueprint::shouldReceive('in')->with('users')->andReturn(collect(['user' => $userBlueprint]));

        // Blueprint::setDirectory(__DIR__.'/../__fixtures__/blueprints/');
        // // $blueprint = Blueprint::make('sites')
        // //     ->setContents(YAML::file(__DIR__.'/../__fixtures__/blueprints/sites.yaml')->parse());

        // // $blueprint->save();

        // copy(
        //     __DIR__.'/../__fixtures__/blueprints/sites.yaml',
        //     '../__fixtures__/blueprints/collections/sites/sites.yaml'
        // );
        // dd('what');
        $collection = (new Collection)
            ->handle('sites');
        // ->entryBlueprint('sites');

        $collection->save();

        /** @var \Statamic\Entries\Entry */
        $entry = Entry::make()
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
}
