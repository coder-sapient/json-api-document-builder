<?php

declare(strict_types=1);

namespace CoderSapient\JsonApi\Document\Builder;

use CoderSapient\JsonApi\Cache\ResourceCache;
use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Exception\ResourceNotFoundException;
use CoderSapient\JsonApi\Registry\ResourceResolverRegistry;
use JsonApiPhp\JsonApi\ResourceCollection;
use JsonApiPhp\JsonApi\ResourceObject;
use function JsonApiPhp\JsonApi\combine;
use function JsonApiPhp\JsonApi\compositeKey;

class Builder
{
    public function __construct(
        protected ResourceResolverRegistry $registry,
        protected ResourceCache $cache,
    ) {
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
        $resolved = $this->findInCache(...$keys);

        $missed = $this->toIdentifiers(
            array_diff($keys, array_keys($resolved)),
        );
        $resolved = array_merge(
            $resolved,
            $this->findByIdentifiers($missed),
        );
        $this->ensureAllResourcesAreFound($includeMap, ...$resolved);

        return $resolved;
    }

    protected function findInCache(string ...$keys): array
    {
        $resources = [];

        foreach ($this->cache->getMany(...$keys) as $resource) {
            $resources[$resource->key()] = $resource;
        }

        return $resources;
    }

    protected function findByIdentifiers(array $identifiers): array
    {
        $resources = [];

        foreach ($identifiers as $resourceType => $resourceIds) {
            $resolver = $this->registry->get($resourceType);

            foreach ($resolver->getByIds(...$resourceIds) as $resource) {
                $resources[$resource->key()] = $resource;
            }
        }

        $this->cache->set(...$resources);

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

    protected function searchByKey(ResourceObject ...$resources): callable
    {
        return static fn (string $key) => $resources[$key] ?? throw new ResourceNotFoundException($key);
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
                $result[] = $fn($this->compositeKey($resourceId, $resourceType));
            }
        }

        return $result;
    }

    protected function toIdentifiers(array $keys): array
    {
        $identifiers = [];

        foreach ($keys as $key) {
            [$resourceType, $resourceId] = $this->splitKey($key);
            $identifiers[$resourceType][] = $resourceId;
        }

        return $identifiers;
    }

    protected function toArray(ResourceCollection $resources): array
    {
        return json_decode(json_encode(combine($resources)->data), true);
    }

    protected function splitKey(string $key, string $delimiter = ':'): array
    {
        return explode($delimiter, $key);
    }

    protected function compositeKey(string $resourceId, string $resourceType): string
    {
        return compositeKey($resourceType, $resourceId);
    }
}
