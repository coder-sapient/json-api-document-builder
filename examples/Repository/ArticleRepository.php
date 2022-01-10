<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Repository;

use CoderSapient\JsonApi\Criteria\Criteria;
use CoderSapient\JsonApi\Examples\Model\Article;

final class ArticleRepository
{
    public function findById(string $id): Article
    {
        return new Article('1', '3', 'first');
    }

    /**
     * @return Article[]
     */
    public function findByIds(string ...$ids): array
    {
        return [new Article('1', '3', 'first')];
    }

    /**
     * You need to match criteria with your query builder.
     *
     * @return Article[]
     */
    public function match(Criteria $criteria): array
    {
        return [new Article('1', '3', 'first')];
    }
}
