<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Action;

use CoderSapient\JsonApi\Document\Builder\SingleDocumentBuilder;
use CoderSapient\JsonApi\Examples\Request\ShowArticleRequest;
use CoderSapient\JsonApi\Exception\JsonApiException;
use JsonApiPhp\JsonApi\JsonApi;

final class ShowArticleAction
{
    public function __construct(private SingleDocumentBuilder $builder)
    {
        $builder->withJsonApi(new JsonApi());
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
