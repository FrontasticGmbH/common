<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\Importer;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\Importer;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\DataStudioClient;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\SearchIndexClient;

class FullImporter implements Importer
{
    /**
     * @var \Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox\Importer[]
     */
    private $importers = [];

    public function __construct(
        SearchIndexClient $searchIndexClient,
        DataStudioClient $dataStudioClient,
        string $locale
    ) {
        $this->importers = [
            //new DataStudioImporter($dataStudioClient, $locale),
            new SearchIndexImporter($searchIndexClient, $locale),
        ];
    }

    public function import(ProductApi $productApi)
    {
        foreach ($this->importers as $importer) {
            $importer->import($productApi);
        }
    }
}
