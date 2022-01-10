<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Action;

use CoderSapient\JsonApi\Document\Builder\SingleDocumentBuilder;
use CoderSapient\JsonApi\Examples\Request\GetArticleRequest;
use CoderSapient\JsonApi\Exception\JsonApiException;

final class GetArticleAction
{
    public function __construct(private SingleDocumentBuilder $builder)
    {
    }

    public function __invoke(GetArticleRequest $request): string
    {
        try {
            $document = $this->builder->build($request->toQuery());
        } catch (JsonApiException $e) {
            return json_encode($e->jsonApiErrors(), \JSON_PRETTY_PRINT);
        }

        return json_encode($document, \JSON_PRETTY_PRINT);
    }
}
