<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Document\Member;

use JsonApiPhp\JsonApi\Meta;

class CountableMember
{
    /**
     * @return Meta[]
     */
    public static function members(int $total, int $page, int $perPage): array
    {
        return [
            new Meta('total', $total),
            new Meta('page', $page),
            new Meta('per_page', $perPage),
            new Meta('last_page', max((int) ceil($total / $perPage), 1)),
        ];
    }
}
