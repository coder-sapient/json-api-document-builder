<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Examples\Repository;

use CoderSapient\JsonApi\Examples\Model\User;
use CoderSapient\JsonApi\Query\DocumentsQuery;

final class UserRepository
{
    public function findById(string $id): User
    {
        return new User('3', 'Bob');
    }

    /**
     * @return User[]
     */
    public function findByIds(string ...$ids): array
    {
        return [new User('3', 'Bob')];
    }

    /**
     * You need to match criteria with your query builder.
     *
     * @return User[]
     */
    public function match(DocumentsQuery $query): array
    {
        return [new User('3', 'Bob')];
    }
}
