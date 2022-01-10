<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Criteria;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;

final class SearchType
{
    public const BY_PREFIX = 'prefix';
    public const BY_PHRASE = 'phrase';

    private const TYPES = [
        self::BY_PREFIX,
        self::BY_PHRASE,
    ];

    public function __construct(private string $type = self::BY_PREFIX)
    {
        if (! in_array($type, self::TYPES, true)) {
            throw new InvalidArgumentException("Invalid search type [{$type}]");
        }
    }

    public function value(): string
    {
        return $this->type;
    }

    public function isPhrase(): bool
    {
        return self::BY_PHRASE === $this->value();
    }
}
