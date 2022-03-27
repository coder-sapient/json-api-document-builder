<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Factory;

use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
use CoderSapient\JsonApi\Resolver\ResourceResolver;

class InMemoryResourceResolverFactory implements ResourceResolverFactory
{
    /** @var ResourceResolver[] */
    private array $resolvers = [];

    /**
     * @param string $resourceType
     *
     * @return ResourceResolver
     *
     * @throws ResourceResolverNotFoundException
     */
    public function make(string $resourceType): ResourceResolver
    {
        if ($this->has($resourceType)) {
            return $this->resolvers[$resourceType];
        }

        throw new ResourceResolverNotFoundException($resourceType);
    }

    /**
     * @param string $resourceType
     * @param ResourceResolver $resolver
     *
     * @return void
     */
    public function add(string $resourceType, ResourceResolver $resolver): void
    {
        $this->resolvers[$resourceType] = $resolver;
    }

    /**
     * @param string $resourceType
     *
     * @return bool
     */
    public function has(string $resourceType): bool
    {
        return isset($this->resolvers[$resourceType]);
    }
}
