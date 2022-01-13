<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Exception;

use Exception;
use JsonApiPhp\JsonApi\ErrorDocument;

abstract class JsonApiException extends Exception
{
    abstract public function jsonApiErrors(): ErrorDocument;

    abstract public function jsonApiStatus(): string;
}
