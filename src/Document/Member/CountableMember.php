<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Document\Member;

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
