<?php

declare(strict_types=1);

namespace Agenciafmd\Nectarcrm\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Message;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

final class SendConversionsToNectarcrm implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(protected array $data = []) {}

    public function handle(): void
    {
        if (! config('laravel-nectarcrm.access_token')) {
            return;
        }

        $this->sendConversion($this->data);
    }

    private function sendConversion(array $data = []): void
    {
        $response = Http::nectarcrm()
            ->withQueryParameters([
                'mail' => $data['emails'][0] ?? null,
            ])
            ->post('contatos/upsert/', $data);

        if ($response->failed()) {
            $this->reportError($response->body());
        }
    }

    private function reportError(string $message, string $subject = 'Falha na integração'): void
    {
        if (!config('laravel-nectarcrm.error_email')) {
            return;
        }

        Mail::raw($message, static function (Message $message) use ($subject) {
            $message->to(config('laravel-nectarcrm.error_email'))
                ->subject('[NectarCrm][' . config('app.url') . '] - ' . $subject . ' - ' . now()->format('d/m/Y H:i:s'));
        });
    }
}
