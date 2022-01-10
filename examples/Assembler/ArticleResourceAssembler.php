<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples\Assembler;

use CoderSapient\JsonApi\Examples\Model\Article;
use JsonApiPhp\JsonApi\Attribute;
use JsonApiPhp\JsonApi\ResourceIdentifier;
use JsonApiPhp\JsonApi\ResourceIdentifierCollection;
use JsonApiPhp\JsonApi\ResourceObject;
use JsonApiPhp\JsonApi\ToMany;

final class ArticleResourceAssembler
{
    /**
     * @return ResourceObject[]
     */
    public function toResources(Article ...$articles): array
    {
        $collect = [];

        foreach ($articles as $article) {
            $collect[] = $this->toResource($article);
        }

        return $collect;
    }

    public function toResource(Article $article): ResourceObject
    {
        return new ResourceObject(
            'articles',
            $article->id(),
            new Attribute('title', $article->title()),
            new ToMany(
                'author',
                new ResourceIdentifierCollection(new ResourceIdentifier('users', $article->authorId())),
            ),
        );
    }
}
