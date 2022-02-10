<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Unit\Document\Builder;

use CoderSapient\JsonApi\Criteria\Chunk;
use CoderSapient\JsonApi\Document\Member\CountableMember;
use CoderSapient\JsonApi\Tests\Assert\AssertDocumentEquals;
use JsonApiPhp\JsonApi\MetaDocument;
use PHPUnit\Framework\TestCase;

final class CountableMemberTest extends TestCase
{
    use AssertDocumentEquals;

    /** @test */
    public function it_should_create_a_meta_objs_with_information_about_the_count_of_resources(): void
    {
        $member1 = CountableMember::members(111, new Chunk(1, 10));
        $member2 = CountableMember::members(0, new Chunk(1, 1));

        self::assertEncodesTo(
            '
            {
               "meta": {
                        "total": "111",
                        "page": "1",
                        "per_page": "10",
                        "last_page": "12"
               }
            }
            ',
            new MetaDocument(...$member1),
        );
        self::assertEncodesTo(
            '
            {
               "meta": {
                        "total": "0",
                        "page": "1",
                        "per_page": "1",
                        "last_page": "1"
               }
            }
            ',
            new MetaDocument(...$member2),
        );
    }
}
