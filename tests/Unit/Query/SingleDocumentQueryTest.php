<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit\Query;

use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Query\SingleDocumentQuery;
use CoderSapient\JsonApi\Utils;
use PHPUnit\Framework\TestCase;

final class SingleDocumentQueryTest extends TestCase
{
    /** @test */
    public function it_should_create_a_valid_single_document_query(): void
    {
        $query = new SingleDocumentQuery('1', 'articles');

        $includes = new Includes(['author']);

        $query1 = $query->setIncludes($includes);
        $query2 = $query1->setIncludes($includes);

        self::assertSame('articles', $query->resourceType());
        self::assertSame('1', $query->resourceId());
        self::assertSame(md5(Utils::compositeKey('articles', '1')), $query->toHash());
        self::assertEquals(new Includes(), $query->includes());
        self::assertNotSame($query, $query1);
        self::assertNotSame($query1, $query2);
        self::assertSame($query1->toHash(), $query2->toHash());
    }
}
