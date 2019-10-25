<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Contentful\Core\Resource\ResourceArray;
use Contentful\Delivery\Client;
use Contentful\Delivery\Resource\Asset;
use Contentful\Delivery\Resource\ContentType as ContentfulContentType;
use Contentful\Delivery\Resource\Entry;
use Contentful\RichText\Node\NodeInterface;
use Contentful\RichText\Renderer;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentType;
use Frontastic\Common\ContentApiBundle\Domain\Query;
use Frontastic\Common\ContentApiBundle\Domain\Result;
use GuzzleHttp\Promise;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Contentful implements ContentApi
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var Renderer
     */
    private $richTextRenderer;

    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct(Client $client, Renderer $richTextRenderer, string $defaultLocale)
    {
        $this->client = $client;
        $this->richTextRenderer = $richTextRenderer;
        $this->defaultLocale = $defaultLocale;
    }

    public function getContentTypes(): array
    {
        return array_map(
            function (ContentfulContentType $contentType): ContentType {
                return new ContentType(
                    [
                        'contentTypeId' => $contentType->getId(),
                        'name' => $contentType->getName(),
                    ]
                );
            },
            iterator_to_array($this->client->getContentTypes())
        );
    }

    public function getContent(string $contentId, string $locale = null, string $mode = self::QUERY_SYNC): ?object
    {
        $locale = $locale ?? $this->defaultLocale;

        $promise = Promise\promise_for(
            $this->client->getEntry($contentId, $this->frontasticToContentfulLocale($locale))
        )->then(function ($entry) {
            return $this->createContentFromEntry($entry);
        });

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;
    }

    public function query(Query $query, string $locale = null, string $mode = self::QUERY_SYNC): ?object
    {
        $contentfulQuery = new \Contentful\Delivery\Query();

        $locale = $locale ?? $this->defaultLocale;
        $contentfulQuery->setLocale($this->frontasticToContentfulLocale($locale));
        if ($query->contentType) {
            $contentfulQuery->setContentType($query->contentType);
        }

        if ($query->contentIds) {
            $contentfulQuery->where('sys.id[in]', $query->contentIds);
        }

        if ($query->query) {
            $contentfulQuery->where('query', $query->query);
        }

        if (!empty($query->attributes)) {
            foreach ($query->attributes as $attribute) {
                $contentfulQuery->where(
                    'fields.' . $attribute->name,
                    $attribute->value
                );
            }
        }

        $promise = Promise\promise_for(
            $this->client->getEntries($contentfulQuery)
        )->then(function (ResourceArray $result) {
            $items = $result->getItems();

            return new Result([
                'total' => $result->getTotal(),
                'count' => count($items),
                'offset' => $result->getSkip(),
                'items' => array_map(
                    [$this, 'createContentFromEntry'],
                    $items
                ),
            ]);
        });

        if ($mode === self::QUERY_SYNC) {
            return $promise->wait();
        }

        return $promise;
    }

    private function createContentFromEntry(Entry $entry): Content
    {
        $name = '';
        $displayField = $entry->getContentType()->getDisplayField();
        if ($displayField !== null) {
            $displayFieldId = $displayField->getId();
            $name = $entry->$displayFieldId;
        }

        $content = new Content([
            'contentId' => $entry->getId(),
            'name' => $name,
            'dangerousInnerContent' => $entry,
        ]);

        $attributes = $this->convertContent($entry, $entry->all());

        $content->attributes = $attributes;

        return $content;
    }

    protected function convertContent(?Entry $entry, iterable $contents): array
    {
        $attributes = [];
        $fieldContentTypes = $entry !== null ? $entry->getContentType()->getFields() : [];

        foreach ($contents as $key => $value) {
            if ($value instanceof Asset) {
                $value = (object)[
                    'url' => 'https:' . $value->getFile()->getUrl(),
                    'title' => $value->getTitle(),
                    'description' => $value->getDescription(),
                ];
            }

            if (is_array($value)) {
                $value = $this->convertContent(null, $value);
            }

            if ($value instanceof Entry) {
                $value = $this->convertContent($value, $value->all());
            }

            if ($value instanceof NodeInterface) {
                $value = $this->richTextRenderer->render($value);
            }

            $type = null;
            if (array_key_exists($key, $fieldContentTypes)) {
                $type = $fieldContentTypes[$key]->getType();
            }

            $attributes[$key] = [
                'attributeId' => $key,
                'content' => $value,
                'type' => $type,
            ];
        }

        return $attributes;
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    private function frontasticToContentfulLocale(string $frontasticLocale): string
    {
        return strtr($frontasticLocale, '_', '-');
    }
}
