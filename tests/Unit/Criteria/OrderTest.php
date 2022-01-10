<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Unit\Criteria;

use CoderSapient\JsonApi\Criteria\Order;
use CoderSapient\JsonApi\Criteria\Orders;
use CoderSapient\JsonApi\Criteria\OrderType;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class OrderTest extends TestCase
{
    /**
     * @test
     * @dataProvider orderTypes
     */
    public function it_should_create_order(string $by, string $type): void
    {
        $order = new Order($by, new OrderType($type));

        self::assertSame($by, $order->by());
        self::assertSame($type, $order->type()->value());
    }

    /** @test */
    public function it_should_create_order_collection(): void
    {
        $collect = new Orders();
        $order = new Order('id', new OrderType());

        self::assertSame(0, $collect->count());
        self::assertTrue($collect->isEmpty());

        $collect->add($order);

        self::assertSame($order, $collect->getIterator()->current());
    }

    /** @test */
    public function it_should_create_order_through_the_factory_methods(): void
    {
        $orderById = Order::fromValues('id', OrderType::ASC);

        self::assertSame('id', $orderById->by());
        self::assertSame(OrderType::ASC, $orderById->type()->value());
    }

    public function orderTypes(): array
    {
        return [['id', OrderType::DESC], ['name', OrderType::ASC]];
    }
}
