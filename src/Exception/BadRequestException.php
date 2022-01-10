<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Exception;

use JsonApiPhp\JsonApi\Error;
use JsonApiPhp\JsonApi\ErrorDocument;

class BadRequestException extends JsonApiException
{
    public function __construct(string $message, private string $source)
    {
        parent::__construct($message);
    }

    public function jsonApiErrors(): ErrorDocument
    {
        return new ErrorDocument(
            new Error(
                new Error\Title('Bad Request'),
                new Error\Status($this->jsonApiStatus()),
                new Error\Detail($this->message),
                new Error\SourceParameter($this->source),
            ),
        );
    }

    public function jsonApiStatus(): string
    {
        return '400';
    }
}
