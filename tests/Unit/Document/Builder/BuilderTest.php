<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Unit\Document\Builder;

use CoderSapient\JsonApi\Cache\InMemoryResourceCache;
use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Document\Builder\Builder;
use CoderSapient\JsonApi\Exception\ResourceNotFoundException;
use CoderSapient\JsonApi\Registry\InMemoryResourceResolverRegistry;
use CoderSapient\JsonApi\Resolver\ResourceResolver;
use CoderSapient\JsonApi\Tests\Assert\AssertDocumentEquals;
use CoderSapient\JsonApi\Tests\Mother\Resource\ResourceMother;
use JsonApiPhp\JsonApi\CompoundDocument;
use JsonApiPhp\JsonApi\Included;
use JsonApiPhp\JsonApi\ResourceCollection;
use PHPUnit\Framework\TestCase;

final class BuilderTest extends TestCase
{
    use AssertDocumentEquals;

    /** @test */
    public function it_should_flatten_includes_and_resolve_them_once(): void
    {
        $user10 = ResourceMother::create('10', 'users');
        $user11 = ResourceMother::create('11', 'users');
        $tass20 = ResourceMother::create('20', 'tags');
        $tags21 = ResourceMother::create('21', 'tags');
        $article1 = ResourceMother::create(
            '1',
            'articles',
            [
                ['authors', 'users', ['10', '11']],
                ['tags', 'tags', ['20', '21']],
            ],
        );
        $article2 = ResourceMother::create(
            '2',
            'articles',
            [
                ['authors', 'users', ['10', '11']],
                ['tags', 'tags', ['20', '21']],
            ],
        );

        $resources = new ResourceCollection($article1, $article2);

        $usersResolver = $this->createMock(ResourceResolver::class);
        $usersResolver->expects(self::once())
            ->method('getByIds')
            ->with(
                self::equalTo('10'),
                self::equalTo('11'),
            )
            ->willReturn([$user10, $user11]);

        $tagsResolver = $this->createMock(ResourceResolver::class);
        $tagsResolver->expects(self::once())
            ->method('getByIds')
            ->with(
                self::equalTo('20'),
                self::equalTo('21'),
            )
            ->willReturn([$tass20, $tags21]);

        $registry = new InMemoryResourceResolverRegistry();
        $registry->add('users', $usersResolver);
        $registry->add('tags', $tagsResolver);

        $builder = new Builder($registry, new InMemoryResourceCache());

        $includes = $builder->buildIncludes(new Includes(['authors', 'tags']), $resources);

        $documents = new CompoundDocument($resources, new Included(...$includes));

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
                            },
                            "tags": {
                                "data": [
                                    {
                                        "type": "tags",
                                        "id": "20"
                                    },
                                    {
                                        "type": "tags",
                                        "id": "21"
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
                                        "id": "10"
                                    },
                                    {
                                        "type": "users",
                                        "id": "11"
                                    }
                                ]
                            },
                            "tags": {
                                "data": [
                                    {
                                        "type": "tags",
                                        "id": "20"
                                    },
                                    {
                                        "type": "tags",
                                        "id": "21"
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
                        "type": "tags",
                        "id": "20"
                    },
                    {
                        "type": "tags",
                        "id": "21"
                    }
                ]
            }',
            $documents,
        );
    }

    /** @test */
    public function it_should_recursively_resolve_all_includes(): void
    {
        $article1 = ResourceMother::create('1', 'articles', [['tags', 'tags', ['20']]]);
        $article2 = ResourceMother::create('2', 'articles', [['tags', 'tags', ['21']]]);
        $tag20 = ResourceMother::create('20', 'tags', [['related_tags', 'tags', ['22']]]);
        $tag21 = ResourceMother::create('21', 'tags', [['related_tags', 'tags', ['23']]]);
        $tag22 = ResourceMother::create('22', 'tags', [['related_tags', 'tags', []]]);
        $tag23 = ResourceMother::create('23', 'tags', [['related_tags', 'tags', []]]);

        $resources = new ResourceCollection($article1, $article2);

        $tagsResolver = $this->createMock(ResourceResolver::class);
        $tagsResolver->expects(self::exactly(2))
            ->method('getByIds')
            ->withConsecutive(['20', '21'], ['22', '23'])
            ->willReturnOnConsecutiveCalls([$tag20, $tag21], [$tag22, $tag23]);

        $registry = new InMemoryResourceResolverRegistry();
        $registry->add('tags', $tagsResolver);

        $builder = new Builder($registry, new InMemoryResourceCache());

        $includes = $builder->buildIncludes(new Includes(['tags.related_tags']), $resources);

        $documents = new CompoundDocument($resources, new Included(...$includes));

        self::assertEncodesTo(
            '{
                "data": [
                    {
                        "type": "articles",
                        "relationships": {
                            "tags": {
                                "data": [
                                    {
                                        "type": "tags",
                                        "id": "20"
                                    }
                                ]
                            }
                        },
                        "id": "1"
                    },
                    {
                        "type": "articles",
                        "relationships": {
                            "tags": {
                                "data": [
                                    {
                                        "type": "tags",
                                        "id": "21"
                                    }
                                ]
                            }
                        },
                        "id": "2"
                    }
                ],
                "included": [
                    {
                        "id": "20",
                        "type": "tags",
                        "relationships": {
                            "related_tags": {
                                "data": [
                                    {
                                        "type": "tags",
                                        "id": "22"
                                    }
                                ]
                            }
                        }
                    },
                    {
                        "id": "21",
                        "type": "tags",
                        "relationships": {
                            "related_tags": {
                                "data": [
                                    {
                                        "type": "tags",
                                        "id": "23"
                                    }
                                ]
                            }
                        }
                    },
                    {
                        "id": "22",
                        "type": "tags",
                        "relationships": {
                            "related_tags": {
                                "data": []
                            }
                        }
                    },
                    {
                        "id": "23",
                        "type": "tags",
                        "relationships": {
                            "related_tags": {
                                "data": []
                            }
                        }
                    }
                ]
            }',
            $documents,
        );
    }

    /** @test */
    public function it_should_throw_an_exception_when_not_all_resources_are_found(): void
    {
        $article1 = ResourceMother::create('1', 'articles', [['tags', 'tags', ['20', '21']]]);
        $tag20 = ResourceMother::create('20', 'tags');
        $tagDummy = ResourceMother::create('dummy', 'tags');

        $resources = new ResourceCollection($article1);

        $tagsResolver = $this->createMock(ResourceResolver::class);
        $tagsResolver->expects(self::once())
            ->method('getByIds')
            ->with(
                self::equalTo('20'),
                self::equalTo('21'),
            )
            ->willReturn([$tag20, $tagDummy]);

        $registry = new InMemoryResourceResolverRegistry();
        $registry->add('tags', $tagsResolver);

        $builder = new Builder($registry, new InMemoryResourceCache());

        $this->expectException(ResourceNotFoundException::class);

        $builder->buildIncludes(new Includes(['tags.related_tags']), $resources);
    }
}
