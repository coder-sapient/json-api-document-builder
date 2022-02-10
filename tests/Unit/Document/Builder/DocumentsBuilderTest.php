<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit\Document\Builder;

use CoderSapient\JsonApi\Cache\InMemoryResourceCache;
use CoderSapient\JsonApi\Document\Builder\DocumentsBuilder;
use CoderSapient\JsonApi\Document\Resolver\ResourceResolver;
use CoderSapient\JsonApi\Registry\InMemoryResourceResolverRegistry;
use CoderSapient\JsonApi\Tests\Assert\AssertDocumentEquals;
use CoderSapient\JsonApi\Tests\Mother\Builder\DocumentQueryMother;
use CoderSapient\JsonApi\Tests\Mother\Resource\ResourceMother;
use PHPUnit\Framework\TestCase;

final class DocumentsBuilderTest extends TestCase
{
    use AssertDocumentEquals;

    /** @test */
    public function it_should_build_documents_with_included_relations(): void
    {
        $query = DocumentQueryMother::compound('articles', ['authors']);

        $article1 = ResourceMother::create('1', 'articles', [['authors', 'users', ['10', '11']]]);
        $article2 = ResourceMother::create('2', 'articles', [['authors', 'users', ['12', '13']]]);
        $user10 = ResourceMother::create('10', 'users');
        $user11 = ResourceMother::create('11', 'users');
        $user12 = ResourceMother::create('12', 'users');
        $user13 = ResourceMother::create('13', 'users');

        $articlesResolver = $this->createMock(ResourceResolver::class);
        $articlesResolver->expects(self::once())
            ->method('resolveMany')
            ->with(self::equalTo($query))
            ->willReturn([$article1, $article2]);

        $usersResolver = $this->createMock(ResourceResolver::class);
        $usersResolver->expects(self::once())
            ->method('resolveByIds')
            ->with(
                self::equalTo('10'),
                self::equalTo('11'),
                self::equalTo('12'),
                self::equalTo('13'),
            )
            ->willReturn([$user10, $user11, $user12, $user13]);

        $registry = new InMemoryResourceResolverRegistry();
        $registry->add('articles', $articlesResolver);
        $registry->add('users', $usersResolver);

        $builder = new DocumentsBuilder($registry, new InMemoryResourceCache());

        self::assertEncodesTo(
            '
             {
                "data": [
                    {
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
                    {
                        "id": "2",
                        "type": "articles",
                        "relationships": {
                            "authors": {
                                "data": [
                                    {
                                        "type": "users",
                                        "id": "12"
                                    },
                                    {
                                        "type": "users",
                                        "id": "13"
                                    }
                                ]
                            }
                        }
                    }
                ],
                "included": [
                    {
                        "type": "users",
                        "id": "10"
                    },
                    {
                        "type": "users",
                        "id": "11"
                    },
                    {
                        "type": "users",
                        "id": "12"
                    },
                    {
                        "type": "users",
                        "id": "13"
                    }
                ]
            }',
            $builder->build($query),
        );
    }

    /** @test */
    public function it_should_only_use_the_cache_to_build_documents(): void
    {
        $query = DocumentQueryMother::compound('articles', ['authors']);

        $article1 = ResourceMother::create('1', 'articles', [['authors', 'users', '10']]);
        $article2 = ResourceMother::create('2', 'articles', [['authors', 'users', '11']]);
        $user10 = ResourceMother::create('10', 'users');
        $user11 = ResourceMother::create('11', 'users');

        $cache = new InMemoryResourceCache();
        $cache->setByKeys($user10, $user11);
        $cache->setByQuery($query, $article1, $article2);

        $articlesResolver = $this->createMock(ResourceResolver::class);
        $articlesResolver->expects(self::never())->method('resolveMany');
        $articlesResolver->expects(self::never())->method('resolveByIds');

        $usersResolver = $this->createMock(ResourceResolver::class);
        $usersResolver->expects(self::never())->method('resolveMany');
        $usersResolver->expects(self::never())->method('resolveByIds');

        $registry = new InMemoryResourceResolverRegistry();
        $registry->add('articles', $articlesResolver);
        $registry->add('users', $usersResolver);

        $builder = new DocumentsBuilder($registry, $cache);

        self::assertEncodesTo(
            '
             {
                "data": [
                    {
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
                    {
                        "id": "2",
                        "type": "articles",
                        "relationships": {
                            "authors": {
                                "data":
                                    {
                                        "type": "users",
                                        "id": "11"
                                    }
                            }
                        }
                    }
                ],
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
}
