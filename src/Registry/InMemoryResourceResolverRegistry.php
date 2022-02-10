<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Registry;

use CoderSapient\JsonApi\Document\Resolver\ResourceResolver;
use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;

class InMemoryResourceResolverRegistry implements ResourceResolverRegistry
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
    public function get(string $resourceType): ResourceResolver
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
