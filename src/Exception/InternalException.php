<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Exception;

use JsonApiPhp\JsonApi\Error;
use JsonApiPhp\JsonApi\ErrorDocument;

class InternalException extends JsonApiException
{
    public function jsonApiErrors(): ErrorDocument
    {
        return new ErrorDocument(
            new Error(
                new Error\Title('Internal Server Error'),
                new Error\Status($this->jsonApiStatus()),
                new Error\Detail($this->getMessage()),
            ),
        );
    }

    public function jsonApiStatus(): string
    {
        return '500';
    }
}
