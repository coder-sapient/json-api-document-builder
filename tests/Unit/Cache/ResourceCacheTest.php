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

        self::assertNull($cache->getByKey($resource1->key()));
        self::assertEmpty($cache->getByKeys($resource1->key()));
        self::assertEmpty($cache->getByCriteria('articles', $criteria));

        $cache->setByKeys($resource1);
        $cache->setByKeys($resource2);
        $cache->setByCriteria('articles', $criteria, $resource3);

        self::assertSame($resource1, $cache->getByKey($resource1->key()));
        self::assertSame($resource2, $cache->getByKey($resource2->key()));
        self::assertSame([$resource1, $resource2], $cache->getByKeys($resource1->key(), $resource2->key()));
        self::assertSame([$resource3], $cache->getByCriteria('articles', $criteria));

        $cache->removeByKeys($resource1->key());
        $cache->removeByKeys($resource2->key());

        self::assertNull($cache->getByKey($resource1->key()));
        self::assertNull($cache->getByKey($resource2->key()));
        self::assertEmpty($cache->getByKeys($resource1->key(), $resource2->key()));

        $cache->setByKeys($resource1);
        $cache->removeByType('articles');

        self::assertNull($cache->getByKey($resource1->key()));
        self::assertEmpty($cache->getByCriteria('articles', $criteria));

        $cache->setByKeys($resource1, $resource2, $resource3);

        $cache->flush();

        self::assertEmpty($cache->getByKeys($resource1->key(), $resource2->key(), $resource3->key()));
    }
}
