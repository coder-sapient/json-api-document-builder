<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Examples;

use CoderSapient\JsonApi\Builder\DocumentsBuilder;
use CoderSapient\JsonApi\Builder\SingleDocumentBuilder;
use CoderSapient\JsonApi\Cache\InMemoryResourceCache;
use CoderSapient\JsonApi\Examples\Action\ListArticlesAction;
use CoderSapient\JsonApi\Examples\Action\ShowArticleAction;
use CoderSapient\JsonApi\Examples\Assembler\ArticleResourceAssembler;
use CoderSapient\JsonApi\Examples\Assembler\UserResourceAssembler;
use CoderSapient\JsonApi\Examples\Repository\ArticleRepository;
use CoderSapient\JsonApi\Examples\Repository\UserRepository;
use CoderSapient\JsonApi\Examples\Resolver\ArticleResourceResolver;
use CoderSapient\JsonApi\Examples\Resolver\UserResourceResolver;
use CoderSapient\JsonApi\Factory\InMemoryResourceResolverFactory;
use CoderSapient\JsonApi\Factory\ResourceResolverFactory;

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

    public static function resourceResolverFactory(): ResourceResolverFactory
    {
        $factory = new InMemoryResourceResolverFactory();

        $factory->add(
            ResourceTypes::ARTICLES,
            self::articleResourceResolver(),
        );
        $factory->add(
            ResourceTypes::USERS,
            self::userResourceResolver(),
        );

        return $factory;
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
            self::resourceResolverFactory(),
            new InMemoryResourceCache(),
        );
    }

    public static function documentsBuilder(): DocumentsBuilder
    {
        return new DocumentsBuilder(
            self::resourceResolverFactory(),
            new InMemoryResourceCache(),
        );
    }
}
