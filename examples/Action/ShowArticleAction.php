<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Examples\Action;

use CoderSapient\JsonApi\Builder\SingleDocumentBuilder;
use CoderSapient\JsonApi\Examples\Request\ShowArticleRequest;
use CoderSapient\JsonApi\Exception\JsonApiException;

final class ShowArticleAction
{
    public function __construct(private SingleDocumentBuilder $builder)
    {
    }

    public function __invoke(ShowArticleRequest $request): string
    {
        try {
            $document = $this->builder->build($request->toQuery());
        } catch (JsonApiException $e) {
            return json_encode($e->jsonApiErrors(), \JSON_PRETTY_PRINT);
        }

        return json_encode($document, \JSON_PRETTY_PRINT);
    }
}
