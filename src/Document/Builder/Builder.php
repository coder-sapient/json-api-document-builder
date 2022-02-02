<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Document\Builder;

use CoderSapient\JsonApi\Cache\ResourceCache;
use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Exception\ResourceNotFoundException;
use CoderSapient\JsonApi\Registry\ResourceResolverRegistry;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\Utils;
use JsonApiPhp\JsonApi\JsonApi;
use JsonApiPhp\JsonApi\Link\RelatedLink;
use JsonApiPhp\JsonApi\Link\SelfLink;
use JsonApiPhp\JsonApi\Meta;
use JsonApiPhp\JsonApi\ResourceCollection;
use JsonApiPhp\JsonApi\ResourceObject;
use function JsonApiPhp\JsonApi\combine;
use function JsonApiPhp\JsonApi\compositeKey;

class Builder
{
    private ?JsonApi $jsonApi = null;
    private ?SelfLink $selfLink = null;
    private ?RelatedLink $relatedLink = null;

    /** @var Meta[] */
    private array $meta = [];

    public function __construct(
        protected ResourceResolverRegistry $registry,
        protected ResourceCache $cache,
    ) {
    }

    public function withJsonApi(JsonApi $jsonApi): self
    {
        $this->jsonApi = $jsonApi;

        return $this;
    }

    public function withSelfLink(SelfLink $selfLink): self
    {
        $this->selfLink = $selfLink;

        return $this;
    }

    public function withRelatedLink(RelatedLink $relatedLink): self
    {
        $this->relatedLink = $relatedLink;

        return $this;
    }

    public function withMeta(Meta ...$meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @return ResourceObject[]
     */
    public function buildIncludes(Includes $includes, ResourceCollection $resources): array
    {
        $includeMap = $this->prepareIncludeMap($includes, $resources);
        $resolved = $this->resolveIncludes($includeMap);

        foreach ($includeMap as $name => $identifiers) {
            if ($includes->partOf($name)->isEmpty()) {
                continue;
            }

            $partition = $this->buildIncludes(
                $includes->partOf($name),
                $this->partOf($identifiers, ...$resolved),
            );

            foreach ($partition as $resource) {
                $resolved[$resource->key()] = $resource;
            }
        }

        return $resolved;
    }

    protected function prepareIncludeMap(Includes $includes, ResourceCollection $resources): array
    {
        $includeMap = [];

        foreach ($this->toArray($resources) as $resource) {
            foreach ($resource['relationships'] as $name => $rel) {
                if (empty($rel['data']) || ! $includes->hasInclude($name)) {
                    continue;
                }
                if (isset($rel['data'][0])) {
                    foreach ($rel['data'] as $data) {
                        $includeMap[$name][$data['type']][$data['id']] = $data['id'];
                    }
                } else {
                    $includeMap[$name][$rel['data']['type']][$rel['data']['id']] = $rel['data']['id'];
                }
            }
        }

        return $includeMap;
    }

    /**
     * @return ResourceObject[]
     */
    protected function resolveIncludes(array $includeMap): array
    {
        $keys = $this->pluckKeys($includeMap);

        $resolved = $this->findByKeys(...$keys);

        $missed = $this->toIdentifiers(array_diff($keys, array_keys($resolved)));

        $resolved = array_merge($resolved, $this->findByIdentifiers($missed));

        $this->ensureAllResourcesAreFound($includeMap, ...$resolved);

        return $resolved;
    }

    protected function findByKeys(string ...$keys): array
    {
        $resources = [];

        foreach ($this->cache->getByKeys(...$keys) as $resource) {
            $resources[$resource->key()] = $resource;
        }

        return $resources;
    }

    protected function findByIdentifiers(array $identifiers): array
    {
        $promises = [];

        foreach ($identifiers as $resourceType => $resourceIds) {
            $resolver = $this->registry->get($resourceType);

            $promises[] = Create::promiseFor(
                $resolver->resolveByIds(...$resourceIds),
            );
        }

        $resources = [];

        foreach (Utils::all($promises)->wait() as $result) {
            /** @var ResourceObject $resource */
            foreach ($result as $resource) {
                $resources[$resource->key()] = $resource;
            }
        }

        $this->cache->setByKeys(...$resources);

        return $resources;
    }

    protected function ensureAllResourcesAreFound(array $includeMap, ResourceObject ...$resources): void
    {
        foreach ($includeMap as $identifiers) {
            $this->applyToIdentifiers(
                $identifiers,
                $this->searchByKey(...$resources),
            );
        }
    }

    protected function partOf(array $identifiers, ResourceObject ...$resources): ResourceCollection
    {
        $partition = $this->applyToIdentifiers(
            $identifiers,
            $this->searchByKey(...$resources),
        );

        return new ResourceCollection(...$partition);
    }

    protected function pluckKeys(array $includeMap): array
    {
        $result = [];

        foreach ($includeMap as $identifiers) {
            $keys = $this->applyToIdentifiers($identifiers, static fn (string $key) => $key);

            foreach ($keys as $key) {
                $result[$key] = $key;
            }
        }

        return $result;
    }

    protected function applyToIdentifiers(array $identifiers, callable $fn): array
    {
        $result = [];

        foreach ($identifiers as $resourceType => $resourceIds) {
            foreach ($resourceIds as $resourceId) {
                $result[] = $fn(compositeKey($resourceType, $resourceId));
            }
        }

        return $result;
    }

    protected function toIdentifiers(array $keys): array
    {
        $identifiers = [];

        foreach ($keys as $key) {
            [$resourceType, $resourceId] = explode(':', $key);
            $identifiers[$resourceType][] = $resourceId;
        }

        return $identifiers;
    }

    protected function members(): array
    {
        return array_merge(
            array_filter([
                $this->jsonApi,
                $this->selfLink,
                $this->relatedLink,
            ]),
            $this->meta,
        );
    }

    protected function reset(): void
    {
        $this->meta = [];
        $this->jsonApi = $this->selfLink = $this->relatedLink = null;
    }

    protected function toArray(ResourceCollection $resources): array
    {
        return json_decode(json_encode(combine($resources)->data), true);
    }

    protected function searchByKey(ResourceObject ...$resources): callable
    {
        return static fn (string $key) => $resources[$key] ?? throw new ResourceNotFoundException($key);
    }
}
