<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Unit\Registry;

use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
use CoderSapient\JsonApi\Registry\InMemoryResourceResolverRegistry;
use CoderSapient\JsonApi\Resolver\ResourceResolver;
use GuzzleHttp\Promise\PromiseInterface;
use JsonApiPhp\JsonApi\ResourceObject;
use PHPUnit\Framework\TestCase;

final class ResourceResolverRegistryTest extends TestCase
{
    /** @test */
    public function in_memory_resource_resolver_registry(): void
    {
        $registry = new InMemoryResourceResolverRegistry();

        $userResolver = $this->resourceResolver();
        $commentResolver = $this->resourceResolver();

        $registry->add('users', $userResolver);
        $registry->add('comments', $commentResolver);

        self::assertSame($userResolver, $registry->get('users'));
        self::assertSame($commentResolver, $registry->get('comments'));
    }

    /** @test */
    public function it_should_throw_an_exception_when_resource_resolver_is_not_found(): void
    {
        $registry = new InMemoryResourceResolverRegistry();

        $this->expectException(ResourceResolverNotFoundException::class);

        $registry->get('users');
    }

    protected function resourceResolver(): ResourceResolver
    {
        return new class () implements ResourceResolver {
            public function getById(string $resourceId): ?ResourceObject
            {
                return null;
            }

            public function getByIds(string ...$resourceIds): array|PromiseInterface
            {
                return [];
            }

            public function matching(Criteria $criteria): array
            {
                return [];
            }
        };
    }
}
