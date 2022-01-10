<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Repository;

use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Examples\Model\User;

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
    public function match(Criteria $criteria): array
    {
        return [new User('3', 'Bob')];
    }
}
