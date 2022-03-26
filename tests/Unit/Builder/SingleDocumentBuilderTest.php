<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit\Builder;

use CoderSapient\JsonApi\Builder\SingleDocumentBuilder;
use CoderSapient\JsonApi\Cache\InMemoryResourceCache;
use CoderSapient\JsonApi\Exception\ResourceNotFoundException;
use CoderSapient\JsonApi\Registry\InMemoryResourceResolverRegistry;
use CoderSapient\JsonApi\Registry\ResourceResolverRegistry;
use CoderSapient\JsonApi\Resolver\ResourceResolver;
use CoderSapient\JsonApi\Tests\Assert\AssertDocumentEquals;
use CoderSapient\JsonApi\Tests\Mother\Builder\DocumentQueryMother;
use CoderSapient\JsonApi\Tests\Mother\Resource\ResourceMother;
use PHPUnit\Framework\TestCase;

final class SingleDocumentBuilderTest extends TestCase
{
    use AssertDocumentEquals;

    /** @test */
    public function it_should_build_single_document_with_included_relations(): void
    {
        $query = DocumentQueryMother::single('articles', '1', ['authors']);

        $article1 = ResourceMother::create('1', 'articles', [['authors', 'users', ['10', '11']]]);
        $user10 = ResourceMother::create('10', 'users');
        $user11 = ResourceMother::create('11', 'users');

        $articlesResolver = $this->createMock(ResourceResolver::class);
        $articlesResolver->expects(self::once())
            ->method('resolveOne')
            ->with(self::equalTo($query))
            ->willReturn($article1);

        $usersResolver = $this->createMock(ResourceResolver::class);
        $usersResolver->expects(self::once())
            ->method('resolveByIds')
            ->with(self::equalTo('10'), self::equalTo('11'))
            ->willReturn([$user10, $user11]);

        $registry = new InMemoryResourceResolverRegistry();
        $registry->add('articles', $articlesResolver);
        $registry->add('users', $usersResolver);

        $builder = new SingleDocumentBuilder($registry, new InMemoryResourceCache());

        self::assertEncodesTo(
            '
            {
                "data": {
                    "id": "1",
                    "type": "articles",
                    "relationships": {
                        "authors": {
                            "data": [
                                {
                                    "type": "users",
                                    "id": "10"
                                },
                                {
                                    "type": "users",
                                    "id": "11"
                                }
                            ]
                        }
                    }
                },
                "included": [
                    {
                        "type": "users",
                        "id": "10"
                    },
                    {
                        "type": "users",
                        "id": "11"
                    }
                ]
            }',
            $builder->build($query),
        );
    }

    /** @test */
    public function it_should_only_use_the_cache_to_build_single_document(): void
    {
        $query = DocumentQueryMother::single('articles', '1', ['authors']);

        $article1 = ResourceMother::create('1', 'articles', [['authors', 'users', '10']]);
        $user10 = ResourceMother::create('10', 'users');

        $cache = new InMemoryResourceCache();
        $cache->setByKeys($article1, $user10);

        $registry = $this->createMock(ResourceResolverRegistry::class);
        $registry->expects(self::never())->method('get');

        $builder = new SingleDocumentBuilder($registry, $cache);

        self::assertEncodesTo(
            '
            {
                "data": {
                    "id": "1",
                    "type": "articles",
                    "relationships": {
                        "authors": {
                            "data":
                                {
                                    "type": "users",
                                    "id": "10"
                                }
                        }
                    }
                },
                "included": [
                    {
                        "type": "users",
                        "id": "10"
                    }
                ]
            }',
            $builder->build($query),
        );
    }

    /** @test */
    public function it_should_throw_an_exception_when_resource_not_found(): void
    {
        $query = DocumentQueryMother::single('articles', '1');

        $articlesResolver = $this->createMock(ResourceResolver::class);
        $articlesResolver->expects(self::once())
            ->method('resolveOne')
            ->with(self::equalTo($query))
            ->willReturn(null);

        $registry = new InMemoryResourceResolverRegistry();
        $registry->add('articles', $articlesResolver);

        $this->expectException(ResourceNotFoundException::class);

        $builder = new SingleDocumentBuilder($registry, new InMemoryResourceCache());

        $builder->build($query);
    }
}
