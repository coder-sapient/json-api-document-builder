<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit\Criteria;

use CoderSapient\JsonApi\Criteria\Filter;
use CoderSapient\JsonApi\Criteria\FilterOperator;
use CoderSapient\JsonApi\Criteria\Filters;
use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class FilterTest extends TestCase
{
    /**
     * @test
     * @dataProvider operators
     */
    public function it_should_create_a_filter(string $field, string $operator, mixed $value): void
    {
        $filter = new Filter($field, new FilterOperator($operator), $value);

        self::assertSame($field, $filter->field());
        self::assertSame($value, $filter->value());
        self::assertTrue($filter->operator()->isEqual($operator));
    }

    /** @test */
    public function it_should_create_filter_collection(): void
    {
        $collect = new Filters();
        $filter = new Filter('filed', new FilterOperator(FilterOperator::EQUAL), 1);

        self::assertSame(0, $collect->count());
        self::assertTrue($collect->isEmpty());

        $collect->add($filter);

        self::assertSame($filter, $collect->getIterator()->current());
    }

    /** @test */
    public function it_should_create_filter_through_the_factory_method(): void
    {
        $field = Filter::fromValues('field', FilterOperator::EQUAL, 1);

        self::assertSame('field', $field->field());
        self::assertSame(1, $field->value());
        self::assertTrue($field->operator()->isEqual(FilterOperator::EQUAL));
    }

    /** @test */
    public function it_should_throw_an_exception_when_filter_operator_invalid(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new FilterOperator('invalid');
    }

    public function operators(): array
    {
        return [
            ['field_1', FilterOperator::EQUAL, 1],
            ['field_1', FilterOperator::NOT_EQUAL, '1'],
            ['field_3', FilterOperator::EQUAL, [1]],
            ['field_4', FilterOperator::NOT_EQUAL, (object) []],
            ['field_5', FilterOperator::GT, '10'],
            ['field_6', FilterOperator::LT, 10],
            ['field_7', FilterOperator::GTE, '100'],
            ['field_8', FilterOperator::LTE, 100],
            ['field_9', FilterOperator::LIKE, 'term'],
        ];
    }
}
