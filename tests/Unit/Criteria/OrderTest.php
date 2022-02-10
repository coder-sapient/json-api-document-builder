<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit\Criteria;

use CoderSapient\JsonApi\Criteria\Order;
use CoderSapient\JsonApi\Criteria\Orders;
use CoderSapient\JsonApi\Criteria\OrderType;
use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

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
    public function it_should_create_order_through_the_factory_method(): void
    {
        $orderById = Order::fromValues('id', OrderType::ASC);

        self::assertSame('id', $orderById->by());
        self::assertSame(OrderType::ASC, $orderById->type()->value());
    }

    /** @test */
    public function it_should_throw_an_exception_when_order_type_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OrderType('invalid');
    }

    public function orderTypes(): array
    {
        return [['id', OrderType::DESC], ['name', OrderType::ASC]];
    }
}
