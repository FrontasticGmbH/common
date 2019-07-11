<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Contentful\Delivery\Client;
use Contentful\Delivery\Resource\Entry;
use Contentful\Delivery\Resource\Asset;
use Contentful\Delivery\ContentType as ContentfulContentType;

use Frontastic\Common\ContentApiBundle\Domain\AttributeFilter;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\ContentType;
use Frontastic\Common\ContentApiBundle\Domain\Category;
use Frontastic\Common\ContentApiBundle\Domain\Query;
use Frontastic\Common\ContentApiBundle\Domain\Result;

class Contentful implements ContentApi
{
    /**
     * @var Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getContentTypes(): array
    {
        return array_map(
            function (ContentfulContentType $contentType): ContentType {
                return new ContentType([
                    'contentTypeId' => $contentType->getId(),
                    'name' => $contentType->getName(),
                ]);
            },
            iterator_to_array($this->client->getContentTypes())
        );
    }

    public function getContent(string $contentId): Content
    {
        return $this->convertContent(
            $this->client->getEntry($contentId)
        );
    }

    public function query(Query $query): Result
    {
        $contentfulQuery = new \Contentful\Delivery\Query();
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
                [$this, 'convertContent'],
                $items
            ),
        ]);
    }

    protected function convertContent(Entry $content): Content
    {
        $contentType = $content->getContentType();
        $displayFieldId = $contentType->getDisplayField()->getId();

        $result = new Content([
            'contentId' => $content->getId(),
            'name' => $content->$displayFieldId,
            'dangerousInnerContent' => $content,
        ]);

        $contents = $content->all();
        foreach ($contents as $key => $value) {
            if ($value instanceof Asset) {
                $value = (object) [
                    'url' => 'https:' . $value->getFile()->getUrl(),
                    'title' => $value->getTitle(),
                    'description' => $value->getDescription(),
                ];
            }

            $result->attributes[$key] = new Attribute([
                'attributeId' => $key,
                'content' => $value,
                'type' => null, //@todo
            ]);
        }

        return $result;
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
