<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class BusinessException extends Exception
{
    public function __construct(
        string $message,
        public readonly string $errorCode,
        public readonly int $status = 409,
        public readonly array $errors = [],
        public readonly ?int $retryAfter = null,
    ) {
        parent::__construct($message);
    }

    public function render(): JsonResponse
    {
        $body = ['message' => $this->getMessage(), 'code' => $this->errorCode];

        if ($this->errors) {
            $body['errors'] = $this->errors;
        }
        if ($this->retryAfter !== null) {
            $body['retry_after'] = $this->retryAfter;
        }

        $response = response()->json($body, $this->status);

        if ($this->retryAfter !== null) {
            $response->header('Retry-After', $this->retryAfter);
        }

        return $response;
    }
}