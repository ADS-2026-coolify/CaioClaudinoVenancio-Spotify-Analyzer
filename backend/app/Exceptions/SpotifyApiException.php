<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\Client\Response;

class SpotifyApiException extends Exception
{
    /**
     * The HTTP status code from Spotify's API.
     */
    public readonly int $statusCode;

    /**
     * The error type returned by Spotify (e.g., "invalid_grant").
     */
    public readonly string $spotifyError;

    /**
     * The human-readable error message from Spotify.
     */
    public readonly string $spotifyMessage;

    /**
     * The Retry-After value in seconds (for rate-limited responses).
     */
    public readonly ?int $retryAfter;

    public function __construct(
        string $message = '',
        int $statusCode = 0,
        string $spotifyError = '',
        string $spotifyMessage = '',
        ?int $retryAfter = null,
        ?Exception $previous = null,
    ) {
        parent::__construct($message, $statusCode, $previous);

        $this->statusCode = $statusCode;
        $this->spotifyError = $spotifyError;
        $this->spotifyMessage = $spotifyMessage;
        $this->retryAfter = $retryAfter;
    }

    /**
     * Create an exception from a Spotify API HTTP response.
     */
    public static function fromResponse(Response $response): static
    {
        $body = $response->json();
        $error = $body['error'] ?? [];

        $spotifyError = is_string($error) ? $error : ($error['message'] ?? 'unknown_error');
        $spotifyMessage = $body['error_description'] ?? ($error['message'] ?? 'An unknown Spotify API error occurred.');
        $statusCode = $response->status();

        $retryAfter = null;
        if ($statusCode === 429) {
            $retryAfter = (int) $response->header('Retry-After');
        }

        return new static(
            message: "Spotify API error [{$statusCode}]: {$spotifyMessage}",
            statusCode: $statusCode,
            spotifyError: $spotifyError,
            spotifyMessage: $spotifyMessage,
            retryAfter: $retryAfter,
        );
    }

    /**
     * Create an unauthorized (401) exception.
     */
    public static function unauthorized(): static
    {
        return new static(
            message: 'Spotify API authentication failed. Token may be expired or revoked.',
            statusCode: 401,
            spotifyError: 'unauthorized',
            spotifyMessage: 'Authentication failed.',
        );
    }

    /**
     * Create a rate-limited (429) exception.
     */
    public static function rateLimited(int $retryAfter): static
    {
        return new static(
            message: "Spotify API rate limited. Retry after {$retryAfter} seconds.",
            statusCode: 429,
            spotifyError: 'rate_limited',
            spotifyMessage: "Rate limited. Retry after {$retryAfter} seconds.",
            retryAfter: $retryAfter,
        );
    }

    /**
     * Create a service unavailable (503) exception.
     */
    public static function serviceUnavailable(): static
    {
        return new static(
            message: 'Spotify API is temporarily unavailable.',
            statusCode: 503,
            spotifyError: 'service_unavailable',
            spotifyMessage: 'Spotify API is temporarily unavailable.',
        );
    }

    /**
     * Check if this is an unauthorized (401) error.
     */
    public function isUnauthorized(): bool
    {
        return $this->statusCode === 401;
    }

    /**
     * Check if this is a rate-limited (429) error.
     */
    public function isRateLimited(): bool
    {
        return $this->statusCode === 429;
    }

    /**
     * Get the Retry-After value in seconds.
     */
    public function getRetryAfter(): ?int
    {
        return $this->retryAfter;
    }
}
