## Features
- Multiple nested paths resource inclusion (e.g. `article, article.author, article.comments.user`).
- Supported filtering operators (`eq`, `neq`, `gt`, `lt`, `gte`, `lte`, `like`).
- Supported sorting, pagination and search by phrase or prefix.

## Requirements

- PHP version &gt;=8.0

## Installation

Use composer to install the package:

```
composer require coder-sapient/json-api-document-builder
```

## Usage

### Request Traits:

- `SingleDocumentRequest`: 
- `DocumentsRequest`:

### SingleDocumentRequest

```php
final class ShowArticleRequest extends Request
{
    use SingleDocumentRequest;

    protected function resourceId(): string
    {
        return '1'; 
    }

    protected function resourceType(): string
    {
        return 'articles';
    }

    protected function supportedIncludes(): array
    {
        return ['author', 'comments'];
    }
}
```

You must define the following methods:
- `resourceId()`: from url ~/articles/{resourceId}
- `resourceType()`: defines the `ResourceResolver::class`
- `supportedIncludes()`: list of supported resource types to include.

`$request->toQuery()` method return `SingleDocumentQuery:class` object that can be handled by `SingleDocumentBuilder::class`

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
        return ['author'];
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

You must define the following methods:
- `resourceType()`: defines the `ResourceResolver::class`
- `supportedIncludes()`: list of supported resource types to include.
- `supportedSorting()`: list of supported rows for sorting.
- `supportedFilters()`: list of supported filters that can be applied to resource collection

`$request->toQuery()` method return `DocumentsQuery:class` object that can be handled by `DocumentsBuilder::class`

### ResourceResolverRegistry

```php
interface ResourceResolverRegistry
{
    /**
     * @throws ResourceResolverNotFoundException
     */
    public function get(string $resourceType): ResourceResolver;
}
```

```php
$registry = new InMemoryResourceResolverRegistry();

$registry->add(
    'articles', // resourceType
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
