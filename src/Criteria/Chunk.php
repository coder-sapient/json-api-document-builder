<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Criteria;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;

class Chunk
{
    public function __construct(private int $page, private int $perPage)
    {
        if ($page < 1 || $perPage < 1) {
            throw new InvalidArgumentException('Page and perPage must be a positive integer');
        }
    }

    public function page(): int
    {
        return $this->page;
    }

    public function perPage(): int
    {
        return $this->perPage;
    }

    public function offset(): int
    {
        return ($this->page() - 1) * $this->perPage();
    }
}
