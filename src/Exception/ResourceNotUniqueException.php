<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Exception;

use JsonApiPhp\JsonApi\Error;
use JsonApiPhp\JsonApi\ErrorDocument;

class ResourceNotUniqueException extends JsonApiException
{
    public function __construct(private string $key)
    {
        parent::__construct("Non Unique [{$key}]");
    }

    public function key(): string
    {
        return $this->key;
    }

    public function jsonApiErrors(): ErrorDocument
    {
        return new ErrorDocument(
            new Error(
                new Error\Title('Resource Non Unique'),
                new Error\Status($this->jsonApiStatus()),
                new Error\Detail($this->getMessage()),
            ),
        );
    }

    public function jsonApiStatus(): string
    {
        return '422';
    }
}
