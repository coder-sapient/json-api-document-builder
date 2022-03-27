<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit\Cache;

use CoderSapient\JsonApi\Cache\InMemoryResourceCache;
use CoderSapient\JsonApi\Tests\Mother\Query\DocumentQueryMother;
use CoderSapient\JsonApi\Tests\Mother\Resource\ResourceMother;
use PHPUnit\Framework\TestCase;

final class ResourceCacheTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_in_memory_resource_cache(): void
    {
        $resource1 = ResourceMother::create('1', 'articles');
        $resource2 = ResourceMother::create('2', 'articles');
        $resource3 = ResourceMother::create('3', 'articles');

        $query = DocumentQueryMother::compound();

        $cache = new InMemoryResourceCache();

        self::assertNull($cache->getByKey($resource1->key()));
        self::assertEmpty($cache->getByKeys($resource1->key()));
        self::assertEmpty($cache->getByQuery($query));

        $cache->setByKeys($resource1);
        $cache->setByKeys($resource2);
        $cache->setByQuery($query, $resource3);

        self::assertSame($resource1, $cache->getByKey($resource1->key()));
        self::assertSame($resource2, $cache->getByKey($resource2->key()));
        self::assertSame([$resource1, $resource2], $cache->getByKeys($resource1->key(), $resource2->key()));
        self::assertSame([$resource3], $cache->getByQuery($query));

        $cache->removeByKeys($resource1->key());
        $cache->removeByKeys($resource2->key());

        self::assertNull($cache->getByKey($resource1->key()));
        self::assertNull($cache->getByKey($resource2->key()));
        self::assertEmpty($cache->getByKeys($resource1->key(), $resource2->key()));

        $cache->setByKeys($resource1);
        $cache->removeByTypes('articles');

        self::assertNull($cache->getByKey($resource1->key()));
        self::assertEmpty($cache->getByQuery($query));

        $cache->setByKeys($resource1, $resource2, $resource3);

        $cache->flush();

        self::assertEmpty($cache->getByKeys($resource1->key(), $resource2->key(), $resource3->key()));
    }
}
