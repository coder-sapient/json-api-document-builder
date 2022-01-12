<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Action;

use CoderSapient\JsonApi\Document\Builder\DocumentsBuilder;
use CoderSapient\JsonApi\Examples\Request\ListArticlesRequest;
use CoderSapient\JsonApi\Exception\JsonApiException;

final class ListArticlesAction
{
    public function __construct(private DocumentsBuilder $builder)
    {
    }

    public function __invoke(ListArticlesRequest $request): string
    {
        try {
            $documents = $this->builder->build($request->toQuery());
        } catch (JsonApiException $e) {
            return json_encode($e->jsonApiErrors(), \JSON_PRETTY_PRINT);
        }

        return json_encode($documents, \JSON_PRETTY_PRINT);
    }
}
