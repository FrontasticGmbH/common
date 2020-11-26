<?php
namespace Frontastic\Common\FindologicBundle\Domain;

use Frontastic\Common\CoreBundle\Domain\RequestProvider;
use Frontastic\Common\FindologicBundle\Domain\ProductSearchApi\Mapper;
use Frontastic\Common\HttpClient;
use Symfony\Component\Routing\Router;

class FindologicMapperFactory
{
    /** @var Router */
    private $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    public function factorForConfigs(
        object $typeSpecificConfig,
        ?object $findologicConfig = null
    ): Mapper {
        $categoryProperty = $typeSpecificConfig->categoryProperty ?? $findologicConfig->categoryProperty ?? null;
        $slugProperty = $typeSpecificConfig->slugProperty ?? $findologicConfig->slugProperty ?? null;
        $slugRegex = null;
        $productRoute = null;

        $routeCollection = $this->router->getRouteCollection();
        if ($productRoute !== null) {
            $productRoute = $routeCollection->get('Frontastic.Frontend.Master.Product.view');
        }

        if ($productRoute !== null) {
            $slugRegex = $productRoute->compile()->getRegex();
        }

        $outputAttributes = $typeSpecificConfig->outputAttributes ?? $findologicConfig->outputAttributes ?? [];

        if (!is_array($outputAttributes)) {
            throw new \RuntimeException('Findologic config option outputAttributes needs to be an array');
        }

        // Ensure the configured category attribute is included in the response
        if ($categoryProperty !== null && strpos('attribute.', $categoryProperty) === 0) {
            $outputAttributes[] = preg_replace('/^attribute\./', '', $categoryProperty);
        }

        return new Mapper($outputAttributes, $categoryProperty, $slugProperty, $slugRegex);
    }
}
