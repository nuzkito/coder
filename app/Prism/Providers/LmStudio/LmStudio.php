<?php

declare(strict_types=1);

namespace App\Prism\Providers\LmStudio;

use App\Prism\Providers\LmStudio\Handlers\Stream;
use App\Prism\Providers\LmStudio\Handlers\Text;
use Generator;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Prism\Prism\Concerns\InitializesClient;
use Prism\Prism\Exceptions\PrismException;
use Prism\Prism\Exceptions\PrismProviderOverloadedException;
use Prism\Prism\Exceptions\PrismRateLimitedException;
use Prism\Prism\Exceptions\PrismRequestTooLargeException;
use Prism\Prism\Providers\Provider;
use Prism\Prism\Text\Request as TextRequest;
use Prism\Prism\Text\Response as TextResponse;

class LmStudio extends Provider
{
    use InitializesClient;

    public function __construct(
        public readonly string $url,
    ) {}

    #[\Override]
    public function text(TextRequest $request): TextResponse
    {
        $handler = new Text($this->client(
            $request->clientOptions(),
            $request->clientRetry()
        ));

        return $handler->handle($request);
    }

    #[\Override]
    public function stream(TextRequest $request): Generator
    {
        $handler = new Stream($this->client(
            $request->clientOptions(),
            $request->clientRetry()
        ));

        return $handler->handle($request);
    }

    public function handleRequestException(string $model, RequestException $e): never
    {
        $statusCode = $e->response->getStatusCode();
        $responseData = $e->response->json();
        $errorMessage = data_get($responseData, 'error.message', 'Unknown error');

        match ($statusCode) {
            400 => throw PrismException::providerResponseError(
                sprintf('LmStudio Bad Request: %s', $errorMessage)
            ),
            401 => throw PrismException::providerResponseError(
                sprintf('LmStudio Authentication Error: %s', $errorMessage)
            ),
            402 => throw PrismException::providerResponseError(
                sprintf('LmStudio Insufficient Credits: %s', $errorMessage)
            ),
            403 => throw PrismException::providerResponseError(
                sprintf('LmStudio Moderation Error: %s', $errorMessage)
            ),
            408 => throw PrismException::providerResponseError(
                sprintf('LmStudio Request Timeout: %s', $errorMessage)
            ),
            413 => throw PrismRequestTooLargeException::make('lmstudio'),
            429 => throw PrismRateLimitedException::make(
                rateLimits: [],
                retryAfter: $e->response->hasHeader('retry-after')
                    ? (int) $e->response->header('retry-after')
                    : null
            ),
            502 => throw PrismException::providerResponseError(
                sprintf('LmStudio Model Error: %s', $errorMessage)
            ),
            503 => throw PrismProviderOverloadedException::make('lmstudio'),
            default => throw PrismException::providerRequestError($model, $e),
        };
    }

    /**
     * @param  array<string, mixed>  $options
     * @param  array<mixed>  $retry
     */
    protected function client(array $options = [], array $retry = [], ?string $baseUrl = null): PendingRequest
    {
        return $this->baseClient()
            ->withOptions($options)
            ->when($retry !== [], fn ($client) => $client->retry(...$retry))
            ->baseUrl($baseUrl ?? $this->url);
    }
}
