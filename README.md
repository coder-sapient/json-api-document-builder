## Json Api Document Builder

This library uses immutable resource objects to efficiently build [Json Api Documents](https://jsonapi.org/format/#document-structure)

## Features

- Pagination, sorting, filtering with the following operators (`eq`, `neq`, `gt`, `lt`, `gte`, `lte`, `like`).
- Multiple nested paths resource inclusion (e.g. `article, article.author, article.comments.user`).
- Async resource inclusion (Guzzle Promises/A+).
- Caching resolved resources.

## Requirements

- PHP version &gt;=8.0

## Installation

Use composer to install the package:

```
composer require coder-sapient/json-api-document-builder
```

## Basic Usage

Request examples:

```
GET /api/v1/articles/{id}?include=author
GET /api/v1/articles?include=author,comments.user
GET /api/v1/articles?sotr=id,-title // sort id in asc, title in desc 
GET /api/v1/articles?filter[id]=100,101&filter[title][like]=value
```

You can add the following traits to your request classes:

- [SingleDocumentRequest](/src/Http/Request/SingleDocumentRequest.php): For documents about a single top-level resource.
- [DocumentsRequest](/src/Http/Request/DocumentsRequest.php): For documents about a collection of top-level resources.

### SingleDocumentRequest

```php
final class ShowArticleRequest extends Request
{
    use SingleDocumentRequest;

    protected function resourceId(): string
    {
        // return from url ~/articles/{resourceId}  
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

| Method                | Description                                                                                                                                      |
|-----------------------|--------------------------------------------------------------------------------------------------------------------------------------------------|
| `resourceId()`        | Returns the resource id that must be taken from url                                                                                              |
| `resourceType()`      | Returns the resource type that defines the [ResourceResolver](#ResourceResolver)                                                                 |
| `supportedIncludes()` | Returns a list of supported relationship names to include                                                                                        |
| `toQuery()`           | Returns the [SingleDocumentQuery](/src/Document/Builder/SingleDocumentQuery.php) object that can be handled by [SingleDocumentBuilder](#Builder) |

### DocumentsRequest

```php
final class ListArticlesRequest extends Request
{
    use DocumentsRequest;

    protected function resourceType(): string
    {
        return 'articles';
    }

    protected function supportedIncludes(): array
    {
        return ['author', 'comments', 'comments.user'];
    }

    protected function supportedSorting(): array
    {
        return ['title'];
    }

    protected function supportedFilters(): array
    {
        return [
            'title' => [FilterOperator::EQUAL, FilterOperator::LIKE],
            'author_id' => [FilterOperator::EQUAL],
        ];
    }
}
```

| Method                | Description                                                                                                                       |
|-----------------------|-----------------------------------------------------------------------------------------------------------------------------------|
| `resourceType()`      | Returns the resource type that defines the [ResourceResolver](#ResourceResolver)                                                  |
| `supportedIncludes()` | Returns a list of supported relationship names to include                                                                         |
| `supportedSorting()`  | Returns a list of supported rows for sorting                                                                                      |
| `supportedFilters()`  | Returns a list of supported filters that can be applied to resource collection                                                    |
| `toQuery()`           | Returns the [DocumentsQuery](/src/Document/Builder/DocumentsQuery.php) object that can be handled by [DocumentsBuilder](#Builder) |

## Builder

To initialize [Builder](/src/Document/Builder/Builder.php), you need to provide instances of [ResourceResolverRegistry](#Registry) and [ResourceCache](#ResourceCache):

| Method                                                             | Description                                         |
|--------------------------------------------------------------------|-----------------------------------------------------|
| `buildIncludes(Includes $includes, ResourceCollection $resources)` | Returns the included collection of resource objects |

The [SingleDocumentBuilder](/src/Document/Builder/SingleDocumentBuilder.php) extends `Builder`:


| Method                                | Description                                       |
|---------------------------------------|---------------------------------------------------|
| `build(SingleDocumentQuery $query)`   | Returns a document with single top-level resource |


The [DocumentsBuilder](/src/Document/Builder/DocumentsBuilder.php) extends `Builder`:


| Method                             | Description                                 |
|------------------------------------|---------------------------------------------|
| `build(DocumentsQuery $query)`     | Returns a document with top-level resources |

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
    'author', 
    new AuthorResourceResolver()
);
$registry->add(
    'comments',
    new CommentsResourceResolver()
);

$builder = new SingleDocumentBuilder($registry,  new InMemoryResourceCache());

$singleDocument = $builder->build($request->toQuery());
```

### ResourceResolver
 
The [Builder](#Builder) use instances of [ResourceResolver](/src/Resolver/ResourceResolver.php) to find resources. 

```php
interface ResourceResolver
{
    public function getById(string $resourceId): ?ResourceObject;

    /**
     * @return ResourceObject[]|PromiseInterface
     */
    public function getByIds(string ...$resourceIds): array|PromiseInterface;

    /**
     * @return ResourceObject[]
     */
    public function matching(Criteria $criteria): array;
}
```

When resolving a collection of top-level resources, it will provide a [Criteria](/src/Criteria/Criteria.php), consisting of filters, sorting, pagination, and search term.
You need to match `Criteria` with your query builder (Doctrine, Eloquent, etc.).

The `Builder` can accept Guzzle Promises when trying to include related resources and load them async.

### PaginationResolver

```php
interface PaginationResolver
{
    public function pagination(Criteria $criteria): Pagination;
}
```

If the `ResourceResolver` implements [PaginationResolver](/src/Resolver/PaginationResolver.php), the `Builder` will add top-level links object to the resulting document.

```
{
  "links": {
    "first": "http://localhost/api/v1/articles?page=1&per_page=15",
    "prev": "http://localhost/api/v1/articles?page=1&per_page=15",
    "next": "http://localhost/api/v1/articles?page=2&per_page=15",
    "last": "http://localhost/api/v1/articles?page=3&per_page=15",
  }
}
```

### CountableResolver

```php
interface CountableResolver
{
    public function count(Criteria $criteria): int;
}
```

If the `ResourceResolver` implements [CountableResolver](/src/Resolver/CountableResolver.php), the `Builder` will add top-level meta objects to the resulting document.

```
{
  "meta": {
    "total": 2,
    "page": 1,
    "per_page": 1,
    "last_page": 2
  }
}
```

### ResourceCache
The `Builder` caches all resolved resources using instance of [ResourceCache](/src/Cache/ResourceCache.php)
There is a basic implementation [InMemoryResourceCache](/src/Cache/InMemoryResourceCache.php).
If you don't need caching, use [NullableResourceCache](/src/Cache/NullableResourceCache.php).
