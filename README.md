## Json Api Document Builder

This framework-agnostic library uses immutable [Resource Objects](https://github.com/json-api-php/json-api) to efficiently build [Json Api Documents](https://jsonapi.org/format/#document-structure).

## Features

- Pagination, sorting, filtering with the following operators (`eq`, `neq`, `gt`, `lt`, `gte`, `lte`, `like`).
- Multiple nested paths resource inclusion (e.g. `article, article.author, article.comments.user`).
- Async resource inclusion (Guzzle Promises/A+).
- Caching resolved resources.
- Fully Unit tested

Request examples:

```
GET /api/v1/articles/{id}?include=author
GET /api/v1/articles?include=author,comments.user&page=1&per_page=15
GET /api/v1/articles?sort=id,-title // sort id in asc, title in desc 
GET /api/v1/articles?filter[id]=100,101&filter[title][like]=value
```

## Requirements

- PHP version &gt;=8.0

## Installation

Use composer to install the package:

```
composer require coder-sapient/json-api-document-builder
```

## Basic Usage

Controller action example:

```php
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
            return json_encode($e->jsonApiErrors());
        }

        return json_encode($document);
    }
}
```

You can add the following traits to your request classes:

- [SingleDocumentRequest](/src/Request/SingleDocumentRequest.php): For documents about a single top-level resource.
- [DocumentsRequest](/src/Request/DocumentsRequest.php): For documents about a collection of top-level resources.

### SingleDocumentRequest

```php
final class ShowArticleRequest extends Request
{
    use SingleDocumentRequest;

    protected function resourceId(): string
    {
        // return from URL ~/articles/{resourceId}  
    }

    protected function resourceType(): string
    {
        return 'articles';
    }

    protected function supportedIncludes(): array
    {
        return ['author', 'comments', 'comments.user'];
    }
}
```

| Method                | Description                                                                                                                                                    |
|-----------------------|----------------------------------------------------------------------------------------------------------------------------------------------------------------|
| `resourceId()`        | Returns the resource id, which should be taken from the URL, for example.                                                                                      |
| `resourceType()`      | Returns the resource type that defines the [ResourceResolver](#ResourceResolver)                                                                               |
| `supportedIncludes()` | Returns a list of supported relationship names to include                                                                                                      |
| `toQuery()`           | Returns the [SingleDocumentQuery](/src/Document/Builder/SingleDocumentQuery.php) object that can be handled by [SingleDocumentBuilder](#SingleDocumentBuilder) |

### DocumentsRequest

```php
final class ListArticlesRequest extends Request
{
    use DocumentsRequest;

    protected function resourceType(): string
    {
        return 'articles';
    }
    
    protected function supportedSorting(): array
    {
        return ['title'];
    }
    
    protected function supportedIncludes(): array
    {
        return ['author', 'comments', 'comments.user'];
    }

    protected function supportedFilters(): array
    {
        return [
            'author_id' => [FilterOperator::EQUAL],
            'title' => [FilterOperator::EQUAL, FilterOperator::LIKE],
        ];
    }
}
```

| Method                | Description                                                                                                                                |
|-----------------------|--------------------------------------------------------------------------------------------------------------------------------------------|
| `resourceType()`      | Returns the resource type that defines the [ResourceResolver](#ResourceResolver)                                                           |
| `supportedIncludes()` | Returns a list of supported relationship names to include                                                                                  |
| `supportedSorting()`  | Returns a list of supported rows for sorting                                                                                               |
| `supportedFilters()`  | Returns a list of supported filters that can be applied to resource collection                                                             |
| `toQuery()`           | Returns the [DocumentsQuery](/src/Document/Builder/DocumentsQuery.php) object that can be handled by [DocumentsBuilder](#DocumentsBuilder) |

## Builder

To initialize [Builder](/src/Document/Builder/Builder.php), you need to provide instances of [ResourceResolverRegistry](#Registry) and [ResourceCache](#ResourceCache):

| Method                                                             | Description                                         |
|--------------------------------------------------------------------|-----------------------------------------------------|
| `buildIncludes(Includes $includes, ResourceCollection $resources)` | Returns the included collection of resource objects |

### SingleDocumentBuilder

The [SingleDocumentBuilder](/src/Document/Builder/SingleDocumentBuilder.php) extends `Builder`:

| Method                              | Description                                       |
|-------------------------------------|---------------------------------------------------|
| `build(SingleDocumentQuery $query)` | Returns a document with single top-level resource |


### DocumentsBuilder

The [DocumentsBuilder](/src/Document/Builder/DocumentsBuilder.php) extends `Builder`:

| Method                         | Description                                 |
|--------------------------------|---------------------------------------------|
| `build(DocumentsQuery $query)` | Returns a document with top-level resources |

## Resolver

### Registry

The [ResourceResolverRegistry](/src/Registry/ResourceResolverRegistry.php) is a container that return a [ResourceResolver](#ResourceResolver) by resource type.

```php
interface ResourceResolverRegistry
{
    /**
     * @throws ResourceResolverNotFoundException
     */
    public function get(string $resourceType): ResourceResolver;
}
```

There is a basic implementation [InMemoryResourceResolverRegistry](/src/Registry/InMemoryResourceResolverRegistry.php):

```php
$registry = new InMemoryResourceResolverRegistry();

$registry->add(
    'articles', // resource type
    new ArticleResourceResolver()
);
$registry->add(
    'users', 
    new AuthorResourceResolver()
);
$registry->add(
    'comments',
    new CommentResourceResolver()
);

$builder = new SingleDocumentBuilder($registry, new InMemoryResourceCache());

$singleDocument = $builder->build($request->toQuery());
```

### ResourceResolver
 
The builder use instances of [ResourceResolver](/src/Resolver/ResourceResolver.php) to find resources by ids or query criteria. 

```php
interface ResourceResolver
{
    public function resolveById(string $resourceId): ?ResourceObject;

    /**
     * @return ResourceObject[]|PromiseInterface
     */
    public function resolveByIds(string ...$resourceIds): array|PromiseInterface;

    /**
     * @return ResourceObject[]
     */
    public function resolveByCriteria(Criteria $criteria): array;
}
```

When resolving a collection of top-level resources, it will provide a [Criteria](/src/Criteria/Criteria.php), consisting of filters, orders, pagination.
You need to match `Criteria` with your query builder (Doctrine, Eloquent, etc.).

The builder can accept Guzzle Promises when trying to include related resources and load them async.

### PaginationResolver

```php
interface PaginationResolver
{
    public function paginate(Criteria $criteria): PaginationResponse;
}
```

If the resource resolver implements [PaginationResolver](/src/Resolver/PaginationResolver.php), the builder will add top-level `Links` and `Meta` objects to the resulting document.

```
{
  "links": {
    "first": "http://localhost/api/v1/articles?page=1&per_page=15",
    "prev": "http://localhost/api/v1/articles?page=1&per_page=15",
    "next": "http://localhost/api/v1/articles?page=2&per_page=15",
    "last": "http://localhost/api/v1/articles?page=3&per_page=15",
  },
  "meta": {
    "total": 45,
    "page": 1,
    "per_page": 15,
    "last_page": 3
  }
}
```

### ResourceCache
```php
/**
 * @see ResourceObject::key()
 */
interface ResourceCache
{
    public function getByKey(string $key): ?ResourceObject;

    /**
     * @return ResourceObject[]
     */
    public function getByKeys(string ...$keys): array;

    /**
     * @return ResourceObject[]
     */
    public function getByCriteria(string $resourceType, Criteria $criteria): array;

    public function setByKeys(ResourceObject ...$resources): void;

    public function setByCriteria(string $resourceType, Criteria $criteria, ResourceObject ...$resources): void;

    public function removeByKeys(string ...$keys): void;

    public function removeByType(string $resourceType): void;

    public function flush(): void;
}
```
The builder caches all resolved resources using instance of [ResourceCache](/src/Cache/ResourceCache.php)
There is a basic implementation [InMemoryResourceCache](/src/Cache/InMemoryResourceCache.php).
If you don't need caching, use [NullableResourceCache](/src/Cache/NullableResourceCache.php).

## License
The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
