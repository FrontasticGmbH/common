<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Contentful\Delivery\Client;
use Contentful\Delivery\Resource\Entry;
use Contentful\Delivery\Resource\Asset;
use Contentful\Delivery\Resource\ContentType as ContentfulContentType;

use Frontastic\Common\ContentApiBundle\Domain\AttributeFilter;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentType;
use Frontastic\Common\ContentApiBundle\Domain\Category;
use Frontastic\Common\ContentApiBundle\Domain\Query;
use Frontastic\Common\ContentApiBundle\Domain\Result;
use Contentful\RichText\Node\NodeInterface;
use Contentful\RichText\Renderer;

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
        $this->client           = $client;
        $this->richTextRenderer = $richTextRenderer;
        $this->defaultLocale    = $defaultLocale;
    }

    public function getContentTypes(): array
    {
        return array_map(
            function (ContentfulContentType $contentType): ContentType {
                return new ContentType(
                    [
                        'contentTypeId' => $contentType->getId(),
                        'name'          => $contentType->getName(),
                    ]
                );
            },
            iterator_to_array($this->client->getContentTypes())
        );
    }

    public function getContent(string $contentId, string $locale = null): Content
    {
        $locale = $locale ?? $this->defaultLocale;

        $entry = $this->client->getEntry($contentId, $this->frontasticToContentfulLocale($locale));

        return $this->createContentFromEntry($entry);
    }

    public function query(Query $query, string $locale = null): Result
    {
        $contentfulQuery = new \Contentful\Delivery\Query();

        $locale = $locale ?? $this->defaultLocale;
        $contentfulQuery->setLocale($this->frontasticToContentfulLocale($locale));
        if ($query->contentType) {
            $contentfulQuery->setContentType($query->contentType);
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

        $result = $this->client->getEntries($contentfulQuery);
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
    }

    private function createContentFromEntry(Entry $entry): Content
    {
        $contentType = $entry->getContentType();

        $displayFieldId = $contentType->getDisplayField()->getId();

        $content = new Content([
            'contentId' => $entry->getId(),
            'name' => $entry->$displayFieldId,
            'dangerousInnerContent' => $entry,
        ]);

        $attributes = $this->convertContent($entry, $entry->all());

        $content->attributes = $attributes;

        return $content;
    }

    protected function convertContent(Entry $entry, iterable $contents): array
    {
        $attributes = [];
        $fieldContentTypes = $entry->getContentType()->getFields();

        foreach ($contents as $key => $value) {
            if ($value instanceof Asset) {
                $value = (object)[
                    'url' => 'https:' . $value->getFile()->getUrl(),
                    'title' => $value->getTitle(),
                    'description' => $value->getDescription(),
                ];
            }

            if (is_array($value)) {
                $value = $this->convertContent($value);
            }

            if ($value instanceof Entry) {
                $value = $this->convertContent($value->all());
            }

            if ($value instanceof NodeInterface) {
                $value = $this->richTextRenderer->render($value);
            }

            $attributes[$key] = [
                'attributeId' => $key,
                'content' => $value,
                'type' => $fieldContentTypes[$key]->getType(),
            ];
        }

        return $attributes;
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    private function contenfulToFrontasticLocale(string $contentfulLocale): string
    {
        return strtr($contentfulLocale, '-', '_');
    }

    private function frontasticToContentfulLocale(string $frontasticLocale): string
    {
        return strtr($frontasticLocale, '_', '-');
    }
}
