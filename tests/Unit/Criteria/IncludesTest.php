<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Tests\Unit\Criteria;

use CoderSapient\JsonApi\Criteria\Includes;
use PHPUnit\Framework\TestCase;

/**
 * @coversNothing
 */
final class IncludesTest extends TestCase
{
    public function testIncludes(): void
    {
        $includes = new Includes([
            'articles',
            'articles.comments',
            'articles.comments.users',
        ]);

        self::assertFalse($includes->isEmpty());
        self::assertFalse($includes->hasInclude('users'));
        self::assertTrue($includes->hasInclude('articles'));
        self::assertSame(['comments', 'comments.users'], $includes->partOf('articles')->toArray());
    }
}
