<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit\Query;

use CoderSapient\JsonApi\Criteria\Chunk;
use CoderSapient\JsonApi\Criteria\Filter;
use CoderSapient\JsonApi\Criteria\FilterOperator;
use CoderSapient\JsonApi\Criteria\Filters;
use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Criteria\Order;
use CoderSapient\JsonApi\Criteria\Orders;
use CoderSapient\JsonApi\Criteria\OrderType;
use CoderSapient\JsonApi\Query\DocumentsQuery;
use PHPUnit\Framework\TestCase;

final class DocumentsQueryTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_documents_query(): void
    {
        $query = new DocumentsQuery('articles');

        $includes = new Includes(['author']);
        $chunk = new Chunk(3, 50);
        $orders = new Orders(Order::fromValues('field', OrderType::ASC));
        $filters = new Filters(Filter::fromValues('field', FilterOperator::EQUAL, 1));

        $query1 = $query
            ->setIncludes($includes)
            ->setFilters($filters)
            ->setOrders($orders)
            ->setChunk($chunk);

        $query2 = $query1
            ->setIncludes($includes)
            ->setFilters($filters)
            ->setOrders($orders)
            ->setChunk($chunk);

        self::assertSame('articles', $query->resourceType());
        self::assertSame(md5(serialize(['articles', new Filters(), new Orders(), new Chunk()])), $query->toHash());
        self::assertEquals(new Includes(), $query->includes());
        self::assertEquals(new Filters(), $query->filters());
        self::assertEquals(new Orders(), $query->orders());
        self::assertEquals(new Chunk(), $query->chunk());
        self::assertNotSame($query, $query1);
        self::assertNotSame($query1, $query2);
        self::assertSame($query1->toHash(), $query2->toHash());
    }
}
