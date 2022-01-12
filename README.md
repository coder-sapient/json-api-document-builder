## Json Api Document Builder

## Features

- Supported filtering operators (`eq`, `neq`, `gt`, `lt`, `gte`, `lte`, `like`).
- Supported sorting, pagination and search by phrase or prefix.
- Multiple nested paths resource inclusion (e.g. `article, article.author, article.comments.user`), possibly async.
- Caching resolved resources.

## Requirements

- PHP version &gt;=8.0

## Installation

Use composer to install the package:

```
composer require coder-sapient/json-api-document-builder
```

## Basic Usage

You can add the following traits to your request classes:

- `SingleDocumentRequest`: For documents about a single top-level resource.
- `DocumentsRequest`: For documents about a collection of top-level resources.

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

| Method                | Description                                                                                     |
|-----------------------|-------------------------------------------------------------------------------------------------|
| `resourceId()`        | Resource id must be taken from url                                                              |
| `resourceType()`      | Resource type that defines the `ResourceResolver::class`                                        |
| `supportedIncludes()` | List of supported resource types to include                                                     |
| `toQuery()`           | Return `SingleDocumentQuery:class` object that can be handled by `SingleDocumentBuilder::class` |

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

| Method                | Description                                                                            |
|-----------------------|----------------------------------------------------------------------------------------|
| `resourceType()`      | Resource type that defines the `ResourceResolver::class`                               |
| `supportedIncludes()` | List of supported resource types to include                                            |
| `supportedSorting()`  | List of supported rows for sorting                                                     |
| `supportedFilters()`  | List of supported filters that can be applied to resource collection                   |
| `toQuery()`           | Return `DocumentsQuery:class` object that can be handled by `DocumentsBuilder::class`  |

### ResourceResolverRegistry

The `ResourceResolverRegistry::class` is a factory that return the corresponding `ResourceResolver::class` by resource
type.

```php
interface ResourceResolverRegistry
{
    /**
     * @throws ResourceResolverNotFoundException
     */
    public function get(string $resourceType): ResourceResolver;
}
```

There is a basic implementation `InMemoryResourceResolverRegistry::class`:

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

| Method         | Description                                               |
|----------------|-----------------------------------------------------------|
| `getById()`    | Return resource object or null                            |
| `getByIds()`   | Return collection of resource objects or Guzzle Promise   |
| `matching()`   | Return collection of resource objects matched by Criteria |

Because resources in a relationship can belong to different services, etc., the Builder can accept Guzzle Promises when trying to include resources and load them async.

### PaginationResolver

```php
interface PaginationResolver
{
    public function pagination(Criteria $criteria): Pagination;
}
```

### CountableResolver

```php
interface CountableResolver
{
    public function count(Criteria $criteria): int;
}
```

### ResourceCache

### Builder
