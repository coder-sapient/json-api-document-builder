<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Member;

use CoderSapient\JsonApi\Criteria\Chunk;
use JsonApiPhp\JsonApi\Meta;

class CountableMember
{
    /**
     * @return Meta[]
     */
    public static function members(int $total, Chunk $chunk): array
    {
        return [
            new Meta('total', $total),
            new Meta('page', $chunk->page()),
            new Meta('per_page', $chunk->perPage()),
            new Meta('last_page', max((int) ceil($total / $chunk->perPage()), 1)),
        ];
    }
}
