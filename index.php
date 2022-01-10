<?php

require __DIR__.'/vendor/autoload.php';

//function oToArray($o): array
//{
//    return json_decode(json_encode($o), true);
//}
//
//use CoderSapient\JsonApi\Cache\InMemoryResourceCache;
//use CoderSapient\JsonApi\Exception\InvalidArgumentException;
//use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
//use CoderSapient\JsonApi\Resolver\ResourceResolver;
//use JsonApiPhp\JsonApi\ResourceObject;
//
//$handlerManager = new class () implements \CoderSapient\JsonApi\Registry\ResourceResolverRegistry {
//
//    private array $handlers;
//
//    public function add(string $type, ResourceResolver $resolver): void
//    {
//        $this->handlers[$type] = $resolver;
//    }
//
//    public function get(string $type): ResourceResolver
//    {
//        if (! $this->has($type)) {
//            throw new ResourceResolverNotFoundException($type);
//        }
//
//        if (is_callable($this->handlers[$type])) {
//            $handler = $this->handlers[$type]();
//
//            if (! $handler instanceof  ResourceResolver) {
//                throw new InvalidArgumentException(
//                    'The DocumentHandler must be an instance of ResourceResolver'
//                );
//            }
//
//            return $handler;
//        }
//
//        return $this->handlers[$type];
//    }
//
//    public function has(string $type): bool
//    {
//        return isset($this->handlers[$type]);
//    }
//};
//$handlerManager->add('articles', new class() implements ResourceResolver {
//
//    public function matching(\CoderSapient\JsonApi\Criteria\Criteria $criteria): array
//    {
//        return [];
//    }
//
//    public function byIds(string ...$ids): array
//    {
//        $dan = new ResourceObject(
//            'people',
//            '9',
//            new \JsonApiPhp\JsonApi\Attribute('first-name', 'Dan'),
//            new \JsonApiPhp\JsonApi\Attribute('last-name', 'Gebhardt'),
//            new \JsonApiPhp\JsonApi\Attribute('twitter', 'dgeb'),
//            new \JsonApiPhp\JsonApi\Link\SelfLink('http://example.com/people/9')
//        );
//
//        $comment05 = new ResourceObject(
//            'comments',
//            '5',
//            new \JsonApiPhp\JsonApi\Attribute('body', 'First!'),
//            new \JsonApiPhp\JsonApi\Link\SelfLink('http://example.com/comments/5'),
//            new \JsonApiPhp\JsonApi\ToOne('author', new \JsonApiPhp\JsonApi\ResourceIdentifier('people', '2'))
//        );
//
//        $comment12 = new ResourceObject(
//            'comments',
//            '12',
//            new \JsonApiPhp\JsonApi\Attribute('body', 'I like XML better'),
//            new \JsonApiPhp\JsonApi\Link\SelfLink('http://example.com/comments/12'),
//            new \JsonApiPhp\JsonApi\ToOne('author', $dan->identifier())
//        );
//
//        return [
//            new ResourceObject(
//                'articles',
//                '1',
//                new \JsonApiPhp\JsonApi\Attribute('title', 'JSON API paints my bikeshed!'),
//                new \JsonApiPhp\JsonApi\Link\SelfLink('http://example.com/articles/1'),
//                new \JsonApiPhp\JsonApi\ToOne(
//                    'author',
//                    $dan->identifier(),
//                    new \JsonApiPhp\JsonApi\Link\SelfLink('http://example.com/articles/1/relationships/author'),
//                    new \JsonApiPhp\JsonApi\Link\RelatedLink('http://example.com/articles/1/author')
//                ),
//                new \JsonApiPhp\JsonApi\ToMany(
//                    'comments',
//                    new \JsonApiPhp\JsonApi\ResourceIdentifierCollection(
//                        $comment05->identifier(),
//                        $comment12->identifier()
//                    ),
//                    new \JsonApiPhp\JsonApi\Link\SelfLink('http://example.com/articles/1/relationships/comments'),
//                    new \JsonApiPhp\JsonApi\Link\RelatedLink('http://example.com/articles/1/comments')
//                )
//            )
//        ];
//    }
//});
//
//$handlerManager->add('people', new class() implements ResourceResolver {
//    public function matching(\CoderSapient\JsonApi\Criteria\Criteria $criteria): array
//    {
//        return [];
//    }
//
//    public function byIds(string ...$ids): array
//    {
//        return [
//            new ResourceObject(
//                'people',
//                '9',
//                new \JsonApiPhp\JsonApi\Attribute('title', 'JSON API paints my bikeshed!'),
//                new \JsonApiPhp\JsonApi\Link\SelfLink('http://example.com/articles/1'),
//                new \JsonApiPhp\JsonApi\ToOne(
//                    'photos',
//                    new \JsonApiPhp\JsonApi\ResourceIdentifier('images', '1'),
//                    new \JsonApiPhp\JsonApi\Link\SelfLink('http://example.com/articles/1/relationships/author'),
//                    new \JsonApiPhp\JsonApi\Link\RelatedLink('http://example.com/articles/1/author')
//                ),
//            )
//        ];
//    }
//});
//$handlerManager->add('comments', new class() implements ResourceResolver {
//    public function matching(\CoderSapient\JsonApi\Criteria\Criteria $criteria): array
//    {
//        return [];
//    }
//
//    public function byIds(string ...$ids): array
//    {
//        return [
//            new ResourceObject(
//                'comments',
//                '5',
//                new \JsonApiPhp\JsonApi\Attribute('title', 'JSON API paints my bikeshed!'),
//                new \JsonApiPhp\JsonApi\Link\SelfLink('http://example.com/articles/1'),
//            ),
//            new ResourceObject(
//                'comments',
//                '12',
//                new \JsonApiPhp\JsonApi\Attribute('title', 'JSON API paints my bikeshed!'),
//                new \JsonApiPhp\JsonApi\Link\SelfLink('http://example.com/articles/1'),
//            )
//        ];
//    }
//});
//$handlerManager->add('images', new class() implements ResourceResolver {
//    public function matching(\CoderSapient\JsonApi\Criteria\Criteria $criteria): array
//    {
//        return [];
//    }
//    public function byIds(string ...$ids): array
//    {
//        return [
//            new ResourceObject(
//                'images',
//                '1',
//                new \JsonApiPhp\JsonApi\Attribute('title', 'JSON API paints my bikeshed!'),
//                new \JsonApiPhp\JsonApi\Link\SelfLink('http://example.com/articles/1'),
//                new \JsonApiPhp\JsonApi\ToOne(
//                    'comments',
//                    new \JsonApiPhp\JsonApi\ResourceIdentifier('comments', '5'),
//                    new \JsonApiPhp\JsonApi\Link\SelfLink('http://example.com/articles/1/relationships/author'),
//                    new \JsonApiPhp\JsonApi\Link\RelatedLink('http://example.com/articles/1/author')
//                ),
//            ),
//        ];
//    }
//});
//
//$builder = new \CoderSapient\JsonApi\Document\Builder\SingleDocumentBuilder($handlerManager, new InMemoryResourceCache());
//$d = (new \CoderSapient\JsonApi\Document\Builder\SingleDocumentQuery('1', 'articles', new \CoderSapient\JsonApi\Criteria\Includes(['author', 'comments', 'author.photos.comments'])));
//
//$document = $builder->buildDocument($d);
//
//dd($document);
