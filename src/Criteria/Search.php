<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Criteria;

final class Search
{
    public function __construct(private string $query, private SearchType $type)
    {
    }

    public static function fromValues(string $query, string $type): self
    {
        return new self($query, new SearchType($type));
    }

    public function query(): string
    {
        return $this->query;
    }

    public function type(): SearchType
    {
        return $this->type;
    }
}
