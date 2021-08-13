<?php

namespace TransformStudios\Uptime\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Statamic\Facades\Entry;
use Statamic\Support\Arr;
use TransformStudios\Uptime\Notifications\AlertCleared;

class WebhookController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    /*
"data" => [
    "service" => [
      "id" => 937828
      "name" => "Erin Test"
      "device_id" => 537094
      "monitoring_service_type" => "HTTP"
      "is_paused" => false
      "msp_address" => "https://transform.share.silentz.dev/!/uptime/test"
      "msp_version" => 2
      "msp_interval" => 5
      "msp_sensitivity" => 2
      "msp_num_retries" => 2
      "msp_url_scheme" => "https"
      "msp_url_path" => "/!/uptime/test"
      "msp_port" => null
      "msp_protocol" => null
      "msp_username" => null
      "msp_proxy" => null
      "msp_dns_server" => null
      "msp_dns_record_type" => null
      "msp_send_string" => null
      "msp_expect_string" => null
      "msp_expect_string_type" => "STRING"
      "msp_encryption" => "SSL_TLS"
      "msp_threshold" => 40
      "msp_notes" => null
      "msp_include_in_global_metrics" => true
      "msp_use_ip_version" => null
      "msp_uptime_sla" => "0.9900"
      "msp_response_time_sla" => "2.200"
      "monitoring_service_type_display" => "HTTP(S)"
      "display_name" => "Erin Test"
      "short_name" => "Erin Test"
      "tags" => [
        "Erin"
      ]
    ]
    "account" => [
      "id" => 151373
      "name" => "Transform Studios"
      "brand" => "uptime"
      "timezone" => "America/Los_Angeles"
      "site_url" => "https://uptime.com"
    ]
    "integration" => [
      "id" => 4310
      "name" => "Erin Test"
      "module" => "webhook"
      "module_verbose_name" => "Custom Postback URL (Webhook)"
      "is_enabled" => true
      "is_errored" => false
      "is_test_supported" => true
      "postback_url" => "https://transform.share.silentz.dev/!/uptime/webhook"
      "headers" => null
      "use_legacy_payload" => false
    ]
    "date" => "2021-08-06T19:16:47.928Z"
    "alert" => [
      "id" => 112738275
      "created_at" => "2021-08-06T19:16:47.928Z"
      "state" => "OK"
      "output" => """
        HTTP OK: HTTP/1.1 204 No Content - 1178 bytes in 0.881 second response time

        time=0.881087s;;;0.000000;40.000000 size=1178B;;;0
        """
      "short_output" => "HTTP OK: HTTP/1.1 204 No Content - 1178 bytes in 0.881 second response time"
      "is_up" => true
    ]
    "global_alert_state" => array:6 [▶]
    "device" => array:5 [▶]
    "downtime" => array:4 [▶]
    "links" => []
  ]
  "event" => "alert_cleared"
    */
    public function __invoke(Request $request)
    {
        $method = 'handle' . Str::studly(str_replace('.', '_', $request->input('event')));

        if (method_exists($this, $method)) {
            return $this->{$method}($request->input('data'));
        }

        return response()->noContent();
    }

    private function handleAlertCleared(array $payload)
    {
        return $this->handleAlert(AlertCleared::class, $payload);
    }

    private function handleAlert($notificationClass, $payload)
    {
        if (! $tag = Arr::get($payload, 'service.tags.0')) {
            return response()->noContent();
        }

        if (! $site = Entry::query()->where('collection', 'sites')->where('uptime_tag', $tag)->first()) {
            return response()->noContent();
        }

        if (! $users = $site->augmentedValue('users')->value()) {
            return response()->noContent();
        }

        Notification::send($users, new $notificationClass($payload));

        return response('Webhook handled');
    }
}
