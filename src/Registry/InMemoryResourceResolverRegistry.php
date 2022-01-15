<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Registry;

use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
use CoderSapient\JsonApi\Resolver\ResourceResolver;

final class InMemoryResourceResolverRegistry implements ResourceResolverRegistry
{
    /** @var ResourceResolver[] */
    private array $resolvers = [];

    public function get(string $resourceType): ResourceResolver
    {
        if ($this->has($resourceType)) {
            return $this->resolvers[$resourceType];
        }

        throw new ResourceResolverNotFoundException($resourceType);
    }

    public function add(string $resourceType, ResourceResolver $resolver): void
    {
        $this->resolvers[$resourceType] = $resolver;
    }

    public function has(string $resourceType): bool
    {
        return isset($this->resolvers[$resourceType]);
    }
}
