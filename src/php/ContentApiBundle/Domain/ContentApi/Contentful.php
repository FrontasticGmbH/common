<?php

namespace Frontastic\Common\ContentApiBundle\Domain\ContentApi;

use Contentful\Delivery\Client;
use Contentful\Delivery\DynamicEntry;
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

    protected function convertContent(DynamicEntry $content): Content
    {
        $contentType = $content->getContentType();
        $displayFieldId = $contentType->getDisplayField()->getId();

        $contents = $content->jsonSerialize()->fields;

        return new Content([
            'contentId' => $content->getId(),
            'name' => $contents->$displayFieldId,
            'attributes' => array_map(
                function ($field) use ($contents): Attribute {
                    return new Attribute([
                        'attributeId' => $field->getId(),
                        'content' => $contents->{$field->getId()} ?? null,
                        'type' => $field->getType(),
                    ]);
                },
                $contentType->getFields()
            ),
            'dangerousInnerContent' => $content,
        ]);
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }
}
