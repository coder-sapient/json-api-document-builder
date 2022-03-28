<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Criteria;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use Countable;

class Includes implements Countable
{
    /**
     * @param array $includes
     * @param string $delimiter
     *
     * @throws InvalidArgumentException
     */
    public function __construct(private array $includes = [], private string $delimiter = '.')
    {
        if (empty($delimiter)) {
            throw new InvalidArgumentException('`delimiter` can not be empty');
        }
    }

    /**
     * @param string $name
     *
     * @return Includes
     *
     * @throws InvalidArgumentException
     */
    public function getPart(string $name): self
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

    /**
     * @param string $name
     *
     * @return bool
     */
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

    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return 0 === $this->count();
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->includes);
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return $this->includes;
    }
}
