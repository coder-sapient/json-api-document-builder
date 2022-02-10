<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Criteria;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;

class OrderType
{
    public const ASC = 'asc';
    public const DESC = 'desc';

    private const ORDER_TYPES = [
        self::ASC,
        self::DESC,
    ];

    /**
     * @param string $type
     *
     * @throws InvalidArgumentException
     */
    public function __construct(private string $type = self::ASC)
    {
        if (! in_array($type, self::ORDER_TYPES, true)) {
            throw new InvalidArgumentException("Invalid order type [{$type}]");
        }
    }

    /**
     * @return string
     */
    public function value(): string
    {
        return $this->type;
    }

    /**
     * @return bool
     */
    public function isAsc(): bool
    {
        return self::ASC === $this->value();
    }
}
