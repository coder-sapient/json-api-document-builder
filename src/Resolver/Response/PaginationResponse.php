<?php

declare(strict_types=1);

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
