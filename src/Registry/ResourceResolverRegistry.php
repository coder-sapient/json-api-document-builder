<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Registry;

use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
use CoderSapient\JsonApi\Resolver\ResourceResolver;

interface ResourceResolverRegistry
{
    /**
     * @throws ResourceResolverNotFoundException
     */
    public function get(string $resourceType): ResourceResolver;
}
