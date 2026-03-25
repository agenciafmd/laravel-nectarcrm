<?php

namespace Agenciafmd\Nectarcrm\Providers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\ServiceProvider;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class HttpServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $this->macros();
    }

    public function macros(): void
    {
        Http::macro('nectarcrm', function () {
            return Http::withMiddleware(static function ($handler) {
                return static function (RequestInterface $request, array $options) use ($handler) {
                    return $handler($request, $options)->then(function (ResponseInterface $response) use ($request) {
                        Log::build([
                            'driver' => 'daily',
                            'path' => storage_path('logs/nectarcrm.log'),
                            'level' => 'debug',
                            'days' => 14,
                        ])
                            ->info(sprintf('%s %s HTTP/%s %s | RESPONSE: %s - %s',
                                $request->getMethod(),
                                $request->getUri(),
                                $request->getProtocolVersion(),
                                $request->getBody(),
                                $response->getStatusCode(),
                                str((string) $response->getBody())->squish()
                            ));

                        return $response;
                    });
                };
            })
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Access-Token' => config('laravel-nectarcrm.access_token'),
                ])
                ->withOptions([
                    'timeout' => 60,
                    'connect_timeout' => 60,
                    'http_errors' => false,
                    'verify' => false,
                ])
                ->baseUrl(config('laravel-nectarcrm.base_url'));
        });
    }
}
