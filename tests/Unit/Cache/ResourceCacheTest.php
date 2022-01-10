<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Unit\Cache;

use CoderSapient\JsonApi\Cache\InMemoryResourceCache;
use CoderSapient\JsonApi\Tests\Mother\Criteria\CriteriaMother;
use CoderSapient\JsonApi\Tests\Mother\Resource\ResourceMother;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class ResourceCacheTest extends TestCase
{
    /** @test */
    public function in_memory_resource_cache(): void
    {
        $resource = ResourceMother::create('1', 'articles');
        $criteria = CriteriaMother::create();
        $cache = new InMemoryResourceCache();

        self::assertNull($cache->getOne($resource->key()));
        self::assertEmpty($cache->getMany($resource->key()));
        self::assertEmpty($cache->getByCriteria('articles', $criteria));

        $cache->set($resource);
        $cache->setByCriteria('articles', $criteria, $resource);

        self::assertSame($resource, $cache->getOne($resource->key()));
        self::assertSame([$resource], $cache->getMany($resource->key()));
        self::assertSame([$resource], $cache->getByCriteria('articles', $criteria));

        $cache->remove($resource->key());
        $cache->removeByCriteria('articles', $criteria);

        self::assertNull($cache->getOne($resource->key()));
        self::assertEmpty($cache->getMany($resource->key()));
        self::assertEmpty($cache->getByCriteria('articles', $criteria));
    }
}
