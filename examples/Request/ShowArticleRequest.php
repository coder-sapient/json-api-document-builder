<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Examples\Request;

use CoderSapient\JsonApi\Examples\ResourceTypes;
use CoderSapient\JsonApi\Request\SingleDocumentRequest;

final class ShowArticleRequest extends Request
{
    use SingleDocumentRequest;

    public function resourceId(): string
    {
        return '1'; // ~/articles/{resourceId}
    }

    public function resourceType(): string
    {
        return ResourceTypes::ARTICLES;
    }

    public function acceptableIncludes(): array
    {
        return ['author'];
    }
}
