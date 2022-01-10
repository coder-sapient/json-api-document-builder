<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Criteria;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use Countable;

class Includes implements Countable
{
    public function __construct(private array $includes = [], private string $delimiter = '.')
    {
        if (empty($delimiter)) {
            throw new InvalidArgumentException('Delimiter can not be empty');
        }
    }

    public function partOf(string $name): self
    {
        $includes = array_map(
            fn (string $include) => mb_substr($include, mb_strlen($name . $this->delimiter)),
            array_filter(
                $this->toArray(),
                fn (string $include) => str_starts_with($include, $name . $this->delimiter),
            ),
        );

        return new self(array_values($includes));
    }

    public function hasInclude(string $name): bool
    {
        foreach ($this->includes as $include) {
            if ($include === $name) {
                return true;
            }

            $parts = explode($this->delimiter, $include);
            if ($parts[0] === $name) {
                return true;
            }
        }

        return false;
    }

    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    public function count(): int
    {
        return count($this->includes);
    }

    public function toArray(): array
    {
        return $this->includes;
    }
}
