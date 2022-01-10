<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Unit\Criteria;

use CoderSapient\JsonApi\Criteria\Search;
use CoderSapient\JsonApi\Criteria\SearchType;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class SearchTest extends TestCase
{
    /**
     * @test
     * @dataProvider searchTypes
     */
    public function it_should_create_search(string $query, string $type): void
    {
        $search = new Search($query, new SearchType($type));

        self::assertSame($query, $search->query());
        self::assertSame($type, $search->type()->value());
    }

    /** @test */
    public function it_should_create_search_through_the_factory_methods(): void
    {
        $search = Search::fromValues('Alisa', SearchType::BY_PHRASE);

        self::assertSame('Alisa', $search->query());
        self::assertSame(SearchType::BY_PHRASE, $search->type()->value());
    }

    public function searchTypes(): array
    {
        return [['Alisa', SearchType::BY_PHRASE], ['Bo', SearchType::BY_PREFIX]];
    }
}
