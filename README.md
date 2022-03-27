## Json Api Document Builder

This library resolve Query Object to [JSON:API](https://jsonapi.org/format/#document-structure) Documents.

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

    protected function acceptableIncludes(): array
    {
        return ['author', 'comments', 'comments.user'];
    }
}
```

| Method                 | Description                                                                                                                                         |
|------------------------|-----------------------------------------------------------------------------------------------------------------------------------------------------|
| `resourceId()`         | Returns the resource id, which should be taken from the URL, for example.                                                                           |
| `resourceType()`       | Returns the resource type that defines the [ResourceResolver](#ResourceResolver)                                                                    |
| `acceptableIncludes()` | Returns a list of acceptable relationship names to include                                                                                          |
| `toQuery()`            | Returns the [SingleDocumentQuery](/src/Query/SingleDocumentQuery.php) object that can be handled by [SingleDocumentBuilder](#SingleDocumentBuilder) |

### DocumentsRequest

```php
final class ListArticlesRequest extends Request
{
    use DocumentsRequest;

    protected function resourceType(): string
    {
        return 'articles';
    }
    
    protected function acceptableSorting(): array
    {
        return ['title', 'created_at'];
    }
    
    protected function acceptableIncludes(): array
    {
        return ['author', 'comments', 'comments.user'];
    }

    protected function acceptableFilters(): array
    {
        return [
            'author_id' => ['eq'],
            'title' => ['eq', 'like'],
        ];
    }
}
```

| Method                 | Description                                                                                                                     |
|------------------------|---------------------------------------------------------------------------------------------------------------------------------|
| `resourceType()`       | Returns the resource type that defines the [ResourceResolver](#ResourceResolver)                                                |
| `acceptableIncludes()` | Returns a list of acceptable relationship names to include                                                                      |
| `acceptableSorting()`  | Returns a list of acceptable rows for sorting                                                                                   |
| `acceptableFilters()`  | Returns a list of acceptable filters that can be applied to resource collection                                                 |
| `toQuery()`            | Returns the [DocumentsQuery](/src/Query/DocumentsQuery.php) object that can be handled by [DocumentsBuilder](#DocumentsBuilder) |

## Builder

To initialize [Builder](/src/Builder/Builder.php), you need to provide instances of [ResourceResolverFactory](#Factory) and [ResourceCache](#ResourceCache):

| Method                                                             | Description                                         |
|--------------------------------------------------------------------|-----------------------------------------------------|
| `buildIncludes(Includes $includes, ResourceCollection $resources)` | Returns the included collection of resource objects |

### SingleDocumentBuilder

The [SingleDocumentBuilder](/src/Builder/SingleDocumentBuilder.php) extends `Builder`:

| Method                              | Description                                       |
|-------------------------------------|---------------------------------------------------|
| `build(SingleDocumentQuery $query)` | Returns a document with single top-level resource |


### DocumentsBuilder

The [DocumentsBuilder](/src/Builder/DocumentsBuilder.php) extends `Builder`:

| Method                         | Description                                 |
|--------------------------------|---------------------------------------------|
| `build(DocumentsQuery $query)` | Returns a document with top-level resources |

## Resolver

### Factory

The [ResourceResolverFactory](/src/Factory/ResourceResolverFactory.php) is a factory that return a [ResourceResolver](#ResourceResolver) by resource type.

```php
interface ResourceResolverFactory
{
    /**
     * @throws ResourceResolverNotFoundException
     */
    public function make(string $resourceType): ResourceResolver;
}
```

There is a basic implementation [InMemoryResourceResolverFactory](/src/Factory/InMemoryResourceResolverFactory.php):

```php
$factory = new InMemoryResourceResolverFactory();

$factory->add(
    'articles', // resource type
    new ArticleResourceResolver()
);
$factory->add(
    'users', 
    new AuthorResourceResolver()
);
$factory->add(
    'comments',
    new CommentResourceResolver()
);

$builder = new SingleDocumentBuilder($factory, new InMemoryResourceCache());

$singleDocument = $builder->build($request->toQuery());
```

### ResourceResolver
 
The builder use instances of [ResourceResolver](/src/Resolver/ResourceResolver.php) to find resources by ids or query criteria. 

```php
interface ResourceResolver
{
    /**
     * @param DocumentsQuery $query
     *
     * @return ResourceObject[]
     */
    public function resolveMany(DocumentsQuery $query): array;

    /**
     * @param SingleDocumentQuery $query
     *
     * @return ResourceObject|null
     */
    public function resolveOne(SingleDocumentQuery $query): ?ResourceObject;

    /**
     * @param string ...$resourceIds
     *
     * @return ResourceObject[]|PromiseInterface
     */
    public function resolveByIds(string ...$resourceIds): array|PromiseInterface;
}
```

When resolving a collection of top-level resources, it will provide a query criteria consisting of filters, orders, pagination.
You need to match criteria with your query builder (Doctrine, Eloquent, etc.).

The builder can accept Guzzle Promises when trying to include related resources and load them async.

### PaginationResolver

```php
interface PaginationResolver
{
    /**
     * @param DocumentsQuery $query
     *
     * @return PaginationResponse
     */
    public function paginate(DocumentsQuery $query): PaginationResponse;
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
The builder caches all resolved resources using instance of [ResourceCache](/src/Cache/ResourceCache.php).
```php
interface ResourceCache
{
    /**
     * @param string $key
     *
     * @return ResourceObject|null
     */
    public function getByKey(string $key): ?ResourceObject;

    /**
     * @return ResourceObject[]
     */
    public function getByKeys(string ...$keys): array;

    /**
     * @param JsonApiQuery $query
     *
     * @return ResourceObject[]
     */
    public function getByQuery(JsonApiQuery $query): array;

    /**
     * @param ResourceObject ...$resources
     *
     * @return void
     */
    public function setByKeys(ResourceObject ...$resources): void;

    /**
     * @param JsonApiQuery $query
     * @param ResourceObject ...$resources
     *
     * @return void
     */
    public function setByQuery(JsonApiQuery $query, ResourceObject ...$resources): void;

    /**
     * @param string ...$keys
     *
     * @return void
     */
    public function removeByKeys(string ...$keys): void;

    /**
     * @param string ...$resourceTypes
     *
     * @return void
     */
    public function removeByTypes(string ...$resourceTypes): void;

    /**
     * @return void
     */
    public function flush(): void;
}
```
There is a basic implementation [InMemoryResourceCache](/src/Cache/InMemoryResourceCache.php).
If you don't need caching, use [NullableResourceCache](/src/Cache/NullableResourceCache.php).

## License
The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
