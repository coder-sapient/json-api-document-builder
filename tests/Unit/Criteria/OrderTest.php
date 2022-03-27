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
     * @dataProvider orders
     */
    public function it_should_create_order(string $field, string $type): void
    {
        $order = new Order($field, new OrderType($type));

        self::assertSame($field, $order->field());
        self::assertSame($type, $order->type()->value());
    }

    /** @test */
    public function it_should_create_empty_order_collection(): void
    {
        $collect = new Orders();

        self::assertSame(0, $collect->count());
        self::assertTrue($collect->isEmpty());
    }

    /** @test */
    public function it_should_create_not_empty_order_collection(): void
    {
        $order = new Order('id', new OrderType());
        $collect = new Orders($order);

        self::assertSame(1, $collect->count());
        self::assertFalse($collect->isEmpty());
        self::assertSame($order, $collect->getIterator()->current());
    }

    /** @test */
    public function it_should_create_order_through_the_factory_method(): void
    {
        $order = Order::fromValues('id', OrderType::ASC);

        self::assertSame('id', $order->field());
        self::assertSame(OrderType::ASC, $order->type()->value());
    }

    /** @test */
    public function it_should_throw_an_exception_when_order_field_is_empty(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new Order('', new OrderType(OrderType::DESC));
    }

    /** @test */
    public function it_should_throw_an_exception_when_order_type_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new OrderType('invalid');
    }

    public function orders(): array
    {
        return [['id', OrderType::DESC], ['name', OrderType::ASC]];
    }
}
