<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\Importer;

use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\Importer;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\SearchIndexClient;
use Frontastic\Common\ProductApiBundle\Domain\Variant;

class SearchIndexImporter implements Importer
{
    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\SearchIndexClient
     */
    private $client;

    /**
     * @var string
     */
    private $locale;

    /**
     * SearchIndexImporter constructor.
     *
     * @param \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\SearchIndexClient $client
     * @param string $locale
     */
    public function __construct(SearchIndexClient $client, string $locale)
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

        /* @todo Semknox error handling */
        print_r($this->client->get('/products/clearInput'));

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
            print_r($this->client->put(
                '/products/batchInput',
                'productsJsonArray=' . rawurlencode(json_encode($products)),
                [],
                []
            ));

            $offset += $limit;
        } while ($result->count > 0);

        /* @todo Semknox error handling */
        print_r($this->client->get('/products/fullUpdate'));
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
            $categoryPath = '_';
            foreach (array_filter(explode('/', $categories[$categoryId]->path)) as $pathId) {
                $categoryPath = $categoryPath . '#' . rawurlencode($categories[$pathId]->name);
            }
            $categoryPaths[] = $categoryPath;
        }

        $mainCategory = array_shift($categoryPaths);
        $secondaryCategories = $categoryPaths;

        $products = [];
        foreach ($product->variants as $variant) {
            $products[] = [
                'name' => $product->name,
                'articleNumber' => $variant->sku,
                'image' => reset($variant->images),
                 // @TODO: Currency should only be stored in context. Property should be removed.
                'price' => $variant->price . ' ' . $variant->currency,
                'description' => $product->description,
                'deeplink' => $product->slug,
                'category' => $mainCategory,
                'secondaryCategories' => $secondaryCategories,
                'groupId' => $product->productId,
                'appendOnly' => (false === $variant->isOnStock),
                'attributes' => $variant->attributes,
                'passOn' => $this->preparePassOn($variant, $product),
            ];
        }
        return $products;
    }

    private function preparePassOn(Variant $variant, Product $product): array
    {
        $passOn = [
            'description' => $product->description,
            'availability' => $variant->isOnStock,
        ];
        foreach ($product->categories as $key => $category) {
            $passOn["category\t{$key}"] = $category;
        }
        foreach ($variant->attributes as $key => $attribute) {
            $passOn["attribute\t{$key}"] = \json_encode($attribute);
        }
        return $passOn;
    }
}
