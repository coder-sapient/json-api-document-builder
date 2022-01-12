<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Examples;

use CoderSapient\JsonApi\Cache\InMemoryResourceCache;
use CoderSapient\JsonApi\Document\Builder\DocumentsBuilder;
use CoderSapient\JsonApi\Document\Builder\SingleDocumentBuilder;
use CoderSapient\JsonApi\Examples\Action\ShowArticleAction;
use CoderSapient\JsonApi\Examples\Action\ListArticlesAction;
use CoderSapient\JsonApi\Examples\Assembler\ArticleResourceAssembler;
use CoderSapient\JsonApi\Examples\Assembler\UserResourceAssembler;
use CoderSapient\JsonApi\Examples\Repository\ArticleRepository;
use CoderSapient\JsonApi\Examples\Repository\UserRepository;
use CoderSapient\JsonApi\Examples\Resolver\ArticleResourceResolver;
use CoderSapient\JsonApi\Examples\Resolver\UserResourceResolver;
use CoderSapient\JsonApi\Registry\InMemoryResourceResolverRegistry;
use CoderSapient\JsonApi\Registry\ResourceResolverRegistry;

class ServiceLocator
{
    public static function getArticleAction(): ShowArticleAction
    {
        return new ShowArticleAction(self::singleDocumentBuilder());
    }

    public static function getArticlesAction(): ListArticlesAction
    {
        return new ListArticlesAction(self::documentsBuilder());
    }

    public static function resourceResolverRegistry(): ResourceResolverRegistry
    {
        $registry = new InMemoryResourceResolverRegistry();

        $registry->add(
            ResourceTypes::ARTICLES,
            self::articleResourceResolver(),
        );
        $registry->add(
            ResourceTypes::USERS,
            self::userResourceResolver(),
        );

        return $registry;
    }

    public static function articleResourceResolver(): ArticleResourceResolver
    {
        return new ArticleResourceResolver(
            new ArticleRepository(),
            new ArticleResourceAssembler(),
        );
    }

    public static function userResourceResolver(): UserResourceResolver
    {
        return new UserResourceResolver(
            new UserRepository(),
            new UserResourceAssembler(),
        );
    }

    public static function singleDocumentBuilder(): SingleDocumentBuilder
    {
        return new SingleDocumentBuilder(
            self::resourceResolverRegistry(),
            new InMemoryResourceCache(),
        );
    }

    public static function documentsBuilder(): DocumentsBuilder
    {
        return new DocumentsBuilder(
            self::resourceResolverRegistry(),
            new InMemoryResourceCache(),
        );
    }
}
