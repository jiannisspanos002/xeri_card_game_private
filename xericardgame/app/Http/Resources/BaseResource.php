<?php

namespace App\Http\Resources;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;

class BaseResource extends JsonResource
{
    protected $message = null;
    protected $statusCode = 200;

    public function withMessage(string $message)
    {
        $this->message = $message;
        return $this;
    }

    public function withStatusCode(int $statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function toArray($request)
    {
        return parent::toArray($request);
    }

    public function with($request)
    {
        $extra = [];

        if ($this->message) {
            $extra['message'] = $this->message;
        }

        return $extra;
    }

    public function toResponse($request): JsonResponse
    {
        $response = parent::toResponse($request);

        return $response->setStatusCode($this->statusCode);
    }
}
