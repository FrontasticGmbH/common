<?php

namespace Frontastic\Common\FindologicBundle\Domain\ProductSearchApi;

use Frontastic\Common\FindologicBundle\Domain\FindologicClient;
use Frontastic\Common\FindologicBundle\Domain\SearchRequest;
use Frontastic\Common\FindologicBundle\Exception\ServiceNotAliveException;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Locale;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use GuzzleHttp\Promise\PromiseInterface;

class FindologicProductSearchApi implements ProductSearchApi
{
    /**
     * @var FindologicClient
     */
    private $client;

    /**
     * @var ProductSearchApi
     */
    private $fallback;

    public function __construct(FindologicClient $client, ProductSearchApi $fallback)
    {
        $this->client = $client;
        $this->fallback = $fallback;
    }

    public function query(ProductQuery $query): PromiseInterface
    {
        $currentCursor = $query->cursor ?? $query->offset ?? null;

        $request = new SearchRequest(
            [
                'query' => $query->query,
                'first' => $currentCursor ?? $query->offset ?? null,
                'count' => $query->limit,
            ]
        );

        $locale = Locale::createFromPosix($query->locale);
        $currency = $locale->currency;

        return $this->client->search($request)
            ->then(
                function ($result) use ($query, $currency, $currentCursor) {
                    $previousCursor = $currentCursor - $query->limit;
                    return new Result(
                        [
                            'query' => clone $query,
                            'offset' => $result['body']['request']['first'],
                            'count' => count($result['body']['result']['items']),
                            'total' => $result['body']['result']['metadata']['totalResults'],
                            'items' => $this->mapProducts($result['body']['result']['items'], $currency),
                            'previousCursor' => $previousCursor < 0 ? null : $previousCursor,
                            'nextCursor' => ($currentCursor) + $query->limit,
                        ]
                    );
                }
            )
            ->otherwise(
                function ($reason) use ($query) {
                    if ($reason instanceof ServiceNotAliveException) {
                        // @TODO log fallback usage
                        return $this->fallback->query($query);
                    }

                    throw $reason;
                }
            );
    }

    public function getSearchableAttributes(): array
    {
        // TODO: Implement getSearchableAttributes() method.
    }

    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    /**
     * @return Product[]
     */
    private function mapProducts(array $items, string $currency): array
    {
        return array_map(
            function ($item) use ($currency) {
                return new Product(
                    [
                        'productId' => $item['id'],
                        'name' => $item['name'],
                        'slug' => $this->getSlugFromUrl($item['url']),
                        'description' => $item['summary'],
                        'categories' => $item['attributes']['cat'],
                        'variants' => empty($item['variants'])
                            ? $this->mapVariants([$item], $item['id'], $currency)
                            : $this->mapVariants($item['variants'], $item['id'], $currency),
                        'dangerousInnerProduct' => $item,
                    ]
                );
            },
            $items
        );
    }

    private function getSlugFromUrl(string $url)
    {
        // @TODO implement slug extraction
        return $url;
    }

    /**
     * @return Variant[]
     */
    private function mapVariants(array $variants, string $itemId, string $currency)
    {
        return array_map(
            function ($variant) use ($itemId, $currency) {
                return new Variant(
                    [
                        'id' => $variant['id'],
                        'sku' => current($variant['ordernumbers']),
                        'groupId' => $itemId,
                        'price' => intval($variant['price'] * 100),
                        'currency' => $currency,
                        'attributes' => $variant['attributes'],
                        'images' => [$variant['imageUrl']],
                        'dangerousInnerVariant' => $variant,
                    ]
                );
            },
            $variants
        );
    }
}
