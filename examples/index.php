<?php

// php -S localhost:8000 - run web server

declare(strict_types=1);

require __DIR__ . '/../vendor/autoload.php';

use CoderSapient\JsonApi\Examples;
use Laminas\Diactoros\ServerRequestFactory;

$request = ServerRequestFactory::fromGlobals();
$path = $request->getUri()->getPath();

if ('/api/v1/articles/1' === $path) { //http://localhost:8000/api/v1/articles/1?include=author
    echo Examples\ServiceLocator::getArticleAction()(new Examples\Request\ShowArticleRequest($request));
} elseif ('/api/v1/articles' === $path) { //http://localhost:8000/api/v1/articles?include=author
    echo Examples\ServiceLocator::getArticlesAction()(new Examples\Request\ListArticlesRequest($request));
} else {
    throw new RuntimeException('Not supported');
}
