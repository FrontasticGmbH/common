<?php

namespace Frontastic\Common\SprykerBundle\Domain\Product;

use Frontastic\Catwalk\FrontendBundle\Routing\ObjectRouter\ProductRouter;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Router;

class SprykerProductRouter extends ProductRouter
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct($container);
        $this->container = $container;
    }

    /**
     * @param Product $product
     * @return string
     */
    public function generateUrlFor(Product $product)
    {
        return $this->getRouter()->generate(
            'Frontastic.Frontend.Master.Product.view',
            [
                'url' => strtr($product->slug, [
                    '_' => '/',
                ]),
                'identifier' => $product->productId
            ]
        );
    }

    /**
     * @return Router
     */
    private function getRouter(): Router
    {
        return $this->container->get('router');
    }
}
