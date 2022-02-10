<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Examples\Repository;

use CoderSapient\JsonApi\Document\Query\DocumentsQuery;
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
    public function match(DocumentsQuery $query): array
    {
        return [new Article('1', '3', 'first')];
    }
}
