<?php

declare(strict_types=1);

/*
 * (c) Yaroslav Khalupiak <i.am.khalupiak@gmail.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CoderSapient\JsonApi\Builder;

use CoderSapient\JsonApi\Cache\ResourceCache;
use CoderSapient\JsonApi\Criteria\Includes;
use CoderSapient\JsonApi\Exception\InvalidArgumentException;
use CoderSapient\JsonApi\Exception\ResourceNotFoundException;
use CoderSapient\JsonApi\Exception\ResourceResolverNotFoundException;
use CoderSapient\JsonApi\Factory\ResourceResolverFactory;
use CoderSapient\JsonApi\Utils as JsonApiUtils;
use GuzzleHttp\Promise\Create;
use GuzzleHttp\Promise\Utils;
use JsonApiPhp\JsonApi\JsonApi;
use JsonApiPhp\JsonApi\Link\RelatedLink;
use JsonApiPhp\JsonApi\Link\SelfLink;
use JsonApiPhp\JsonApi\Meta;
use JsonApiPhp\JsonApi\ResourceCollection;
use JsonApiPhp\JsonApi\ResourceObject;

use function JsonApiPhp\JsonApi\combine;

class Builder
{
    /** @var Meta[] */
    private array $meta = [];

    /** @var JsonApi|null */
    private ?JsonApi $jsonApi = null;

    /** @var SelfLink|null */
    private ?SelfLink $selfLink = null;

    /** @var RelatedLink|null */
    private ?RelatedLink $relatedLink = null;

    /**
     * @param ResourceResolverFactory $factory
     * @param ResourceCache $cache
     */
    public function __construct(
        protected ResourceResolverFactory $factory,
        protected ResourceCache $cache,
    ) {
    }

    /**
     * @param JsonApi $jsonApi
     *
     * @return $this
     */
    public function withJsonApi(JsonApi $jsonApi): self
    {
        $this->jsonApi = $jsonApi;

        return $this;
    }

    /**
     * @param Meta ...$meta
     *
     * @return $this
     */
    public function withMeta(Meta ...$meta): self
    {
        $this->meta = $meta;

        return $this;
    }

    /**
     * @param SelfLink $selfLink
     *
     * @return $this
     */
    public function withSelfLink(SelfLink $selfLink): self
    {
        $this->selfLink = $selfLink;

        return $this;
    }

    /**
     * @param RelatedLink $relatedLink
     *
     * @return $this
     */
    public function withRelatedLink(RelatedLink $relatedLink): self
    {
        $this->relatedLink = $relatedLink;

        return $this;
    }

    /**
     * Get the included resources.
     *
     * @param Includes $includes
     * @param ResourceCollection $resources
     *
     * @return ResourceObject[]
     *
     * @throws InvalidArgumentException
     * @throws ResourceResolverNotFoundException
     */
    public function buildIncludes(Includes $includes, ResourceCollection $resources): array
    {
        if (
            $includes->isEmpty()
            || [] === $relationships = $this->prepareRelationships($includes, $resources)
        ) {
            return [];
        }

        $includedResources = $this->resolveRelationships($relationships);

        foreach ($relationships as $name => $identifiers) {
            $includesPart = $includes->getPart($name);

            if ($includesPart->isEmpty()) {
                continue;
            }

            $includedResourcesPart = $this->buildIncludes(
                $includesPart,
                $this->getPart($identifiers, ...$includedResources),
            );

            foreach ($includedResourcesPart as $resource) {
                $includedResources[$resource->key()] = $resource;
            }
        }

        return $includedResources;
    }

    /**
     * Get relationship identifiers to include.
     *
     * @param Includes $includes
     * @param ResourceCollection $resources
     *
     * @return array
     */
    protected function prepareRelationships(Includes $includes, ResourceCollection $resources): array
    {
        $relationships = [];

        foreach ($this->toArray($resources) as $resource) {
            foreach ($resource['relationships'] as $name => $relation) {
                if (empty($relation['data']) || ! $includes->hasInclude($name)) {
                    continue;
                }
                if (isset($relation['data'][0])) {
                    foreach ($relation['data'] as $data) {
                        $relationships[$name]
                        [$data['type']]
                        [$data['id']] = $data['id'];
                    }
                } else {
                    $relationships[$name]
                    [$relation['data']['type']]
                    [$relation['data']['id']] = $relation['data']['id'];
                }
            }
        }

        return $relationships;
    }

    /**
     * Get resources by relationship identifiers.
     *
     * @param array $relationships
     *
     * @return ResourceObject[]
     *
     * @throws ResourceResolverNotFoundException
     */
    protected function resolveRelationships(array $relationships): array
    {
        $keys = $this->toKeys($relationships);

        $resolved = $this->findByKeys(...$keys);

        $missed = $this->toIdentifiers(array_diff($keys, array_keys($resolved)));

        if ([] !== $missed) {
            $resolved = array_merge($resolved, $this->findByIdentifiers($missed));
        }

        $this->ensureAllRelationsAreFound($relationships, ...$resolved);

        return $resolved;
    }

    /**
     * Get resources from cache by composite keys.
     *
     * @param string ...$keys
     *
     * @return ResourceObject[]
     */
    protected function findByKeys(string ...$keys): array
    {
        $resources = [];

        foreach ($this->cache->getByKeys(...$keys) as $resource) {
            $resources[$resource->key()] = $resource;
        }

        return $resources;
    }

    /**
     * Get resources from resolver by relationship identifiers.
     *
     * @param array $identifiers
     *
     * @return ResourceObject[]
     *
     * @throws ResourceResolverNotFoundException
     */
    protected function findByIdentifiers(array $identifiers): array
    {
        $promises = [];

        foreach ($identifiers as $resourceType => $resourceIds) {
            $resolver = $this->factory->make($resourceType);

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

    /**
     * Ensure that all requested relationships are found.
     *
     * @param array $relationships
     * @param ResourceObject ...$resources
     *
     * @return void
     */
    protected function ensureAllRelationsAreFound(array $relationships, ResourceObject ...$resources): void
    {
        foreach ($relationships as $identifiers) {
            $this->applyTo(
                $identifiers,
                $this->searchByKey(...$resources),
            );
        }
    }

    /**
     * Get part of the resource collection by relationship identifiers.
     *
     * @param array $identifiers
     * @param ResourceObject ...$resources
     *
     * @return ResourceCollection
     */
    protected function getPart(array $identifiers, ResourceObject ...$resources): ResourceCollection
    {
        $partition = $this->applyTo(
            $identifiers,
            $this->searchByKey(...$resources),
        );

        return new ResourceCollection(...$partition);
    }

    /**
     * Get composite keys from relationship identifiers.
     *
     * @param array $relationships
     *
     * @return array
     */
    protected function toKeys(array $relationships): array
    {
        $result = [];

        foreach ($relationships as $identifiers) {
            $keys = $this->applyTo($identifiers, static fn (string $key) => $key);

            foreach ($keys as $key) {
                $result[$key] = $key;
            }
        }

        return $result;
    }

    /**
     * Get relationship identifiers from composite keys.
     *
     * @param array $keys
     *
     * @return array
     */
    protected function toIdentifiers(array $keys): array
    {
        $identifiers = [];

        foreach ($keys as $key) {
            [$resourceType, $resourceId] = JsonApiUtils::splitKey($key);
            $identifiers[$resourceType][] = $resourceId;
        }

        return $identifiers;
    }

    /**
     * Apply function to relationship identifiers.
     *
     * @param array $identifiers
     * @param callable $fn
     *
     * @return array
     */
    protected function applyTo(array $identifiers, callable $fn): array
    {
        $result = [];

        foreach ($identifiers as $resourceType => $resourceIds) {
            foreach ($resourceIds as $resourceId) {
                $result[] = $fn(JsonApiUtils::compositeKey($resourceType, $resourceId));
            }
        }

        return $result;
    }

    /**
     * Get top-level document members.
     *
     * @return array
     */
    protected function members(): array
    {
        return array_merge(
            $this->meta,
            array_filter([$this->jsonApi, $this->selfLink, $this->relatedLink]),
        );
    }

    /**
     * Reset builder state.
     *
     * @return void
     */
    protected function reset(): void
    {
        $this->meta = [];
        $this->jsonApi = $this->selfLink = $this->relatedLink = null;
    }

    /**
     * Get array representation of JSON:API resources.
     *
     * @param ResourceCollection $resources
     *
     * @return array
     */
    protected function toArray(ResourceCollection $resources): array
    {
        return json_decode(json_encode(combine($resources)), true)['data'];
    }

    /**
     * Search resource by composite key.
     *
     * @param ResourceObject ...$resources
     *
     * @return callable
     */
    protected function searchByKey(ResourceObject ...$resources): callable
    {
        return static fn (string $key) => $resources[$key] ?? throw new ResourceNotFoundException($key);
    }
}
