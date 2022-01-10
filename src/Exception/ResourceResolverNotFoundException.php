<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Exception;

class ResourceResolverNotFoundException extends InternalException
{
    public function __construct(private string $resourceType)
    {
        parent::__construct("Resource resolver for type [{$resourceType}] is not registered");
    }

    public function resourceType(): string
    {
        return $this->resourceType;
    }
}
