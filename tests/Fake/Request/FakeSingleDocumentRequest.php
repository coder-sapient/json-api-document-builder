<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Tests\Fake\Request;

use CoderSapient\JsonApi\Request\SingleDocumentRequest;

final class FakeSingleDocumentRequest
{
    use SingleDocumentRequest;

    public function __construct(private array $queryParams)
    {
    }

    public function queryParams(): array
    {
        return $this->queryParams;
    }

    public function resourceId(): string
    {
        return '1';
    }

    public function resourceType(): string
    {
        return 'articles';
    }

    public function acceptableIncludes(): array
    {
        return ['author', 'comments'];
    }
}
