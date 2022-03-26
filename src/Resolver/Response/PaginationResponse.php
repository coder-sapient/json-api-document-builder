<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Resolver\Response;

use JsonApiPhp\JsonApi\Pagination;

class PaginationResponse
{
    public function __construct(private int $total, private Pagination $pagination)
    {
    }

    public function total(): int
    {
        return $this->total;
    }

    public function pagination(): Pagination
    {
        return $this->pagination;
    }
}
