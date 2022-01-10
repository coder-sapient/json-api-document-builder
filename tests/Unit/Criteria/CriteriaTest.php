<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Unit\Criteria;

use CoderSapient\JsonApi\Criteria\Filter;
use CoderSapient\JsonApi\Criteria\Filters;
use CoderSapient\JsonApi\Tests\Mother\Criteria\CriteriaMother;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class CriteriaTest extends TestCase
{
    /** @test */
    public function it_should_compare_the_keys_of_criteria(): void
    {
        $criteria1 = CriteriaMother::create();
        $criteria2 = CriteriaMother::create();
        $criteria3 = CriteriaMother::create(new Filters(Filter::fromValues('field', 'eq', 1)));
        $criteria4 = CriteriaMother::create(new Filters(Filter::fromValues('field', 'eq', 1)));

        self::assertSame($criteria1->key(), $criteria2->key());
        self::assertSame($criteria3->key(), $criteria4->key());
        self::assertNotSame($criteria1->key(), $criteria3->key());
        self::assertNotSame($criteria2->key(), $criteria4->key());
    }
}
