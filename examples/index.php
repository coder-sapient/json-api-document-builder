<?php

// php -S localhost:8000

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

require __DIR__ . '/../vendor/autoload.php';

use CoderSapient\JsonApi\Examples;
use Laminas\Diactoros\ServerRequestFactory;

$request = ServerRequestFactory::fromGlobals();
$path = $request->getUri()->getPath();

if ('/api/v1/articles/1' === $path && 'GET' === $request->getMethod()) { //http://localhost:8000/api/v1/articles/1?include=author
    echo Examples\ServiceLocator::getArticleAction()(new Examples\Request\ShowArticleRequest($request));
} elseif ('/api/v1/articles' === $path && 'GET' === $request->getMethod()) { //http://localhost:8000/api/v1/articles?include=author
    echo Examples\ServiceLocator::getArticlesAction()(new Examples\Request\ListArticlesRequest($request));
} else {
    echo 'Not Supported';
}
