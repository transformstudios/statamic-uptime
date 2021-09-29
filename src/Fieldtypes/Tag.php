<?php

namespace TransformStudios\Uptime\Fieldtypes;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Statamic\Fieldtypes\Relationship;

class Tag extends Relationship
{
    protected static $handle = 'uptime_tag';

    public function getIndexItems($request)
    {
        $response = Http::withToken(config('uptime.api_key'), 'Token')
            ->get('https://uptime.com/api/v1/check-tags?page_size=1000');

        return collect(Arr::get($response->json(), 'results'))
            ->map(fn (array $tagData) => ['id' => $tagData['tag'], 'title' => $tagData['tag']])
            ->sortBy('title');
    }

    // public function preProcessIndex($id)
    // {
    //     if (is_null($id)) {
    //         return;
    //     }

    //     return [[
    //         'id' => $id,
    //         'title' => $id,
    //     ]];
    // }

    protected function toItemArray($id)
    {
        if (! $id) {
            return [];
        }

        return [
            'id' => $id,
            'title' => $id,
        ];
    }
}
