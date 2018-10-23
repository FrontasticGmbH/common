<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\Importer;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\DataStudioClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\Importer;

class DataStudioImporter implements Importer
{
    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\DataStudioClient
     */
    private $client;

    /**
     * @var string
     */
    private $locale;

    /**
     * @var string
     * @todo Where should we get the host from?
     */
    private $host = 'demo.frontastic.io.local';

    /**
     * SearchIndexImporter constructor.
     *
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\DataStudioClient $client
     * @param string $locale
     */
    public function __construct(DataStudioClient $client, string $locale)
    {
        $this->client = $client;
        $this->locale = $locale;
    }

    public function import(ProductApi $api)
    {
        /* @todo Hard coded limit to categories */
        $categories = $api->getCategories(new CategoryQuery([
            'locale' => $this->locale,
            'limit' => 500
        ]));

        $categoriesById = [];
        foreach ($categories as $category) {
            $categoriesById[$category->categoryId] = $category;
        }

        print_r($this->client->delete('/products'));

        $offset = 0;
        $limit = 200;
        do {
            $result = $api->query(new ProductQuery([
                'locale' => $this->locale,
                'offset' => $offset,
                'limit' => $limit,
            ]));

            $products = [];
            foreach ($result as $product) {
                $products = array_merge($products, $this->prepare($product, $categoriesById));
            }

            /* @todo Semknox error handling */
            print_r($this->client->post(
                '/products',
                'products=' . rawurlencode(json_encode($products))
            ));

            $offset += $limit;
        } while ($result->count > 0);
    }

    /**
     * @param \Frontastic\Common\ProductApiBundle\Domain\Product $product
     * @param \Frontastic\Common\ProductApiBundle\Domain\Category[] $categories
     * @return array
     */
    private function prepare(Product $product, array $categories): array
    {
        $categoryPaths = [];
        foreach ($product->categories as $categoryId) {
            $categoryPath = [];
            foreach (array_filter(explode('/', $categories[$categoryId]->path)) as $pathId) {
                $categoryPath[] = $categories[$pathId]->name;
            }
            $categoryPaths[] = $categoryPath;
        }

        $products = [];
        foreach ($product->variants as $variant) {
            //$attributes = [['key' => '_currency', 'value' => $variant->currency]];
            foreach ($variant->attributes as $key => $value) {
                if (false === is_array($value)) {
                    $attributes[] = ['key' => $key, 'value' => (string) $value];
                }
            }

            $products[] = [
                'title' => $product->name,
                'articleNumber' => $variant->sku,
                'categoryPaths' => $categoryPaths,
                'image' => reset($variant->images),
                'price' => $variant->price + 0.00000001,
                'descriptions' => [$product->description],
                'url' => sprintf('//%s/%s', $this->host, $product->slug),
                 // @TODO: Currency should only be stored in context. Property should be removed.
                'currency' => $variant->currency,
                //'groupId' => $variant->groupId,
                //'appendOnly' => (false === $variant->isOnStock),
                'attributes' => $attributes,/*
                'passOn' => [
                    'availability' => $variant->isOnStock,
                    'categories' => join("\t", $product->categories),
                    'attributes' => json_encode($product->attributes)
                ],*/
            ];
        }
        return $products;
    }
}
