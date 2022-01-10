<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Request;

use Psr\Http\Message\ServerRequestInterface;

abstract class Request
{
    public function __construct(protected ServerRequestInterface $request)
    {
    }

    public function queryParams(): array
    {
        return $this->request->getQueryParams();
    }
}
