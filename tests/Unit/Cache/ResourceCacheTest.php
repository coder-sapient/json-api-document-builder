<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Unit\Cache;

use CoderSapient\JsonApi\Cache\InMemoryResourceCache;
use CoderSapient\JsonApi\Tests\Mother\Criteria\CriteriaMother;
use CoderSapient\JsonApi\Tests\Mother\Resource\ResourceMother;
use PHPUnit\Framework\TestCase;

final class ResourceCacheTest extends TestCase
{
    /** @test */
    public function in_memory_resource_cache(): void
    {
        $resource1 = ResourceMother::create('1', 'articles');
        $resource2 = ResourceMother::create('2', 'articles');
        $resource3 = ResourceMother::create('3', 'articles');

        $criteria = CriteriaMother::create();

        $cache = new InMemoryResourceCache();

        self::assertNull($cache->getOne($resource1->key()));
        self::assertEmpty($cache->getMany($resource1->key()));
        self::assertEmpty($cache->getByCriteria('articles', $criteria));

        $cache->set($resource1);
        $cache->set($resource2);
        $cache->setByCriteria('articles', $criteria, $resource3);

        self::assertSame($resource1, $cache->getOne($resource1->key()));
        self::assertSame($resource2, $cache->getOne($resource2->key()));
        self::assertSame([$resource1, $resource2], $cache->getMany($resource1->key(), $resource2->key()));
        self::assertSame([$resource3], $cache->getByCriteria('articles', $criteria));

        $cache->remove($resource1->key());
        $cache->remove($resource2->key());
        $cache->removeByCriteria('articles', $criteria);

        self::assertNull($cache->getOne($resource1->key()));
        self::assertNull($cache->getOne($resource2->key()));
        self::assertEmpty($cache->getMany($resource1->key(), $resource2->key()));
        self::assertEmpty($cache->getByCriteria('articles', $criteria));

        $cache->set($resource1, $resource2, $resource3);

        self::assertSame(
            [
                $resource1,
                $resource2,
                $resource3,
            ],
            $cache->getMany(
                $resource1->key(),
                $resource2->key(),
                $resource3->key(),
            ),
        );


        $cache->flush();

        self::assertEmpty($cache->getMany($resource1->key(), $resource2->key(), $resource3->key()));
    }
}
