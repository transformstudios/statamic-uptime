<?php

namespace TransformStudios\Uptime;

use Illuminate\Support\Facades\Http;

class Tags extends \Statamic\Tags\Tags
{
    protected static $handle = 'uptime';

    public function outages()
    {
        $tag = $this->params->get('tag');
        $result = Http::withToken(config('uptime.api_key'), 'Token')
            ->get("https://uptime.com/api/v1/outages/?check_tag=$tag&ongoing=true");

        return $result->json('results');
    }
}
