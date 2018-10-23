<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Semknox;

use Frontastic\Common\ProductApiBundle\Domain\ProductApi;

interface Importer
{
    public function import(ProductApi $productApi);
}
