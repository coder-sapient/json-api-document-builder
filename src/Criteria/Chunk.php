<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Criteria;

use CoderSapient\JsonApi\Exception\InvalidArgumentException;

class Chunk
{
    /**
     * @param int $page
     * @param int $perPage
     *
     * @throws InvalidArgumentException
     */
    public function __construct(private int $page = 1, private int $perPage = 15)
    {
        if ($page < 1 || $perPage < 1) {
            throw new InvalidArgumentException('`page` and `perPage` must be a positive integer');
        }
    }

    /**
     * @return int
     */
    public function page(): int
    {
        return $this->page;
    }

    /**
     * @return int
     */
    public function perPage(): int
    {
        return $this->perPage;
    }

    /**
     * @return int
     */
    public function offset(): int
    {
        return ($this->page() - 1) * $this->perPage();
    }
}
