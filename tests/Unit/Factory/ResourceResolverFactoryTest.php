<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit\Factory;

use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
use CoderSapient\JsonApi\Factory\InMemoryResourceResolverFactory;
use CoderSapient\JsonApi\Resolver\ResourceResolver;
use PHPUnit\Framework\TestCase;

final class ResourceResolverFactoryTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_in_memory_resource_resolver_factory(): void
    {
        $factory = new InMemoryResourceResolverFactory();

        $userResolver = $this->createMock(ResourceResolver::class);
        $commentResolver = $this->createMock(ResourceResolver::class);

        $factory->add('users', $userResolver);
        $factory->add('comments', $commentResolver);

        self::assertSame($userResolver, $factory->make('users'));
        self::assertSame($commentResolver, $factory->make('comments'));
    }

    /** @test */
    public function in_memory_resource_resolver_should_throw_an_exception_when_resource_resolver_is_not_found(): void
    {
        $factory = new InMemoryResourceResolverFactory();

        $this->expectException(ResourceResolverNotFoundException::class);

        $factory->make('users');
    }
}
