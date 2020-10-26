<?php

namespace Frontastic\Common\ApiTests;

use Frontastic\Catwalk\ApiCoreBundle\Domain\Context;
use Frontastic\Catwalk\ApiCoreBundle\Domain\ContextService;
use Frontastic\Common\AccountApiBundle\Domain\Account;
use Frontastic\Common\AccountApiBundle\Domain\AccountApi;
use Frontastic\Common\AccountApiBundle\Domain\AccountApiFactory;
use Frontastic\Common\AccountApiBundle\Domain\Session;
use Frontastic\Common\CartApiBundle\Domain\CartApi;
use Frontastic\Common\CartApiBundle\Domain\CartApiFactory;
use Frontastic\Common\ContentApiBundle\Domain\ContentApi;
use Frontastic\Common\ContentApiBundle\Domain\DefaultContentApiFactory;
use Frontastic\Common\EnvironmentResolver;
use Frontastic\Common\ProductApiBundle\Domain\Category;
use Frontastic\Common\ProductApiBundle\Domain\Product;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\CategoryQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Query\ProductQuery;
use Frontastic\Common\ProductApiBundle\Domain\ProductApi\Result;
use Frontastic\Common\ProductApiBundle\Domain\ProductApiFactory;
use Frontastic\Common\ProductApiBundle\Domain\Variant;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApi;
use Frontastic\Common\ProductSearchApiBundle\Domain\ProductSearchApiFactory;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApiFactory;
use Frontastic\Common\ReplicatorBundle\Domain\CustomerService;
use Frontastic\Common\ReplicatorBundle\Domain\Project;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\Account\SessionService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FrontasticApiTestCase extends KernelTestCase
{
    const NON_EXISTING_SLUG = 'THIS_SLUG_SHOULD_NEVER_EXIST_IN_ANY_DATA_SET';

    const URI_PATH_SEGMENT_REGEX = '/^([0-9a-zA-Z_.~-]|%[0-9A-F]{2})+$/';

    /**
     * @var array<string, ProductApi>
     */
    private $productApis = [];

    /**
     * @var array<string, ProductSearchApi>
     */
    private $productSearchApis = [];

    /**
     * @before
     */
    protected function setUpKernel(): void
    {
        $environmentResolver = new EnvironmentResolver();
        $environmentResolver->loadEnvironmentVariables(
            [
                dirname(__DIR__, 5),
                dirname(__DIR__, 2),
                __DIR__,
            ]
        );
        self::bootKernel();
    }

    public function setUp(): void
    {
        $account = new Account(['accountId' => uniqid()]);
        $session = new Session(['account' => $account, 'loggedIn' => false]);
        $contextMock = $this->getMockBuilder(Context::class)->getMock();
        $contextMock->session = $session;
        $contextServiceMock = $this
            ->getMockBuilder(ContextService::class)
            ->setMethods(['createContextFromRequest'])
            ->disableOriginalConstructor()
            ->getMock();
        $contextServiceMock
            ->method('createContextFromRequest')
            ->willReturn($contextMock);

        $sessionServiceMock = $this
            ->getMockBuilder(SessionService::class)
            ->setMethods(['getSessionId'])
            ->getMock();

        $sessionServiceMock
            ->method('getSessionId')
            ->willReturn(uniqid());

        self::$kernel->getContainer()
            ->set(
                'Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper',
                new AccountHelper($contextServiceMock, $sessionServiceMock)
            );
    }

    public function customerAndProject(): array
    {
        $customerService = new CustomerService(__DIR__ . '/config/customers', '');

        $projects = [];
        foreach ($customerService->getCustomers() as $customer) {
            if ($customer->configuration['test']->disabled ?? false === true) {
                continue;
            }

            foreach ($customer->projects as $project) {
                $description = sprintf(
                    'customer: %s, project: %s (ID %s)',
                    $customer->name,
                    $project->name,
                    $project->projectId
                );
                $projects[$description] = [$customer, $project];
            }
        }
        return $projects;
    }

    public function project(): array
    {
        return array_map(
            function (array $customerAndProject): array {
                return [$customerAndProject[1]];
            },
            $this->customerAndProject()
        );
    }

    private function hasContentApiConfig(Project $project): bool
    {
        if ($project->configuration['content']->engine == 'contentful' &&
            key_exists('accessToken', $project->configuration['content']) &&
            key_exists('previewToken', $project->configuration['content']) &&
            key_exists('spaceId', $project->configuration['content'])
        ) {
            return true;
        }

        if ($project->configuration['content']->engine == 'graphcms' &&
            key_exists('apiToken', $project->configuration['content']) &&
            key_exists('apiVersion', $project->configuration['content']) &&
            key_exists('projectId', $project->configuration['content']) &&
            key_exists('region', $project->configuration['content']) &&
            key_exists('stage', $project->configuration['content'])
        ) {
            return true;
        }

        return false;
    }

    public function projectAndLanguage(): array
    {
        $projectsAndLocales = [];

        foreach ($this->project() as $projectDescription => [$project]) {
            if ($this->hasContentApiConfig($project)) {
                continue;
            }

            foreach ($project->languages as $language) {
                $projectsAndLocales[$projectDescription . ', language: ' . $language] = [
                    $project,
                    $language,
                ];
            }
        }

        return $projectsAndLocales;
    }

    public function projectAndLanguageForContentApi(): array
    {
        $projectsAndLocales = [];

        foreach ($this->project() as $projectDescription => [$project]) {
            if (!$this->hasContentApiConfig($project)) {
                continue;
            }

            foreach ($project->languages as $language) {
                $projectsAndLocales[$projectDescription . ', language: ' . $language] = [
                    $project,
                    $language,
                ];
            }
        }

        return $projectsAndLocales;
    }

    /**
     * @param string[] $values
     */
    protected function assertArrayHasDistinctValues(array $values): void
    {
        foreach (array_count_values($values) as $value => $count) {
            $this->assertEquals(1, $count, 'Value ' . $value . ' occurred more then once');
        }
    }

    protected function assertNotEmptyString($actual, string $message = ''): void
    {
        $this->assertIsString($actual, $message);
        $this->assertNotEquals('', $actual, $message);
    }

    protected function assertIsValidTranslatedLabel(Project $project, $label): void
    {
        $this->assertIsArray($label);
        $this->assertContainsOnly('string', $label);
        $this->assertEquals($project->languages, array_keys($label));
    }

    protected function assertProductVariantIsWellFormed(Variant $variant, bool $priceIsRequired = true): void
    {
        $this->assertNotEmptyString($variant->id);
        $this->assertNotEmptyString($variant->sku);

        $this->assertNotEmptyString($variant->groupId);

        if ($priceIsRequired) {
            $this->assertNotNull($variant->price);
        }
        if ($variant->price !== null) {
            $this->assertIsInt($variant->price);
            $this->assertGreaterThanOrEqual(0, $variant->price);
            $this->assertNotEmptyString($variant->currency);
        } else {
            $this->assertNull($variant->discountedPrice);
            $this->assertNull($variant->currency);
        }

        if ($variant->discountedPrice !== null) {
            $this->assertIsInt($variant->discountedPrice);
            $this->assertGreaterThanOrEqual(0, $variant->discountedPrice);
            $this->assertLessThanOrEqual($variant->price, $variant->discountedPrice);
        }

        $this->assertIsArray($variant->discounts);

        $this->assertIsArray($variant->attributes);

        $this->assertIsArray($variant->images);
        foreach ($variant->images as $image) {
            $this->assertNotEmptyString($image);
        }

        $this->assertIsBool($variant->isOnStock);

        $this->assertNull($variant->dangerousInnerVariant);
    }

    protected function getSearchableAttributesForProjectWithProjectSearchApi(Project $project)
    {
        return self::$container
            ->get(ProductSearchApiFactory::class)
            ->factor($project)
            ->getSearchableAttributes()
            ->wait();
    }

    protected function getSearchableAttributesForProject(Project $project)
    {
        return self::$container
            ->get(ProjectApiFactory::class)
            ->factor($project)
            ->getSearchableAttributes();
    }

    protected function getProductApiForProject(Project $project): ProductApi
    {
        $key = sprintf('%s_%s', $project->customer, $project->projectId);
        if (!array_key_exists($key, $this->productApis)) {
            $this->productApis[$key] = self::$container->get(ProductApiFactory::class)->factor($project);
        }

        return $this->productApis[$key];
    }

    protected function queryProducts(
        Project $project,
        string $language,
        array $queryParameters = [],
        ?int $limit = null,
        ?int $offset = null,
        ?string $cursor = null
    ): Result {
        $query = new ProductQuery(
            array_merge(
                $this->buildQueryParameters($language, $limit, $offset, $cursor),
                $queryParameters
            )
        );
        $result = $this
            ->getProductApiForProject($project)
            ->query($query, ProductApi::QUERY_ASYNC)
            ->wait();

        $this->assertEquals($query, $result->query);
        $this->assertNotSame($query, $result->query);

        return $result;
    }


    protected function getProductSearchApiForProject(Project $project): ProductSearchApi
    {
        $key = sprintf('%s_%s', $project->customer, $project->projectId);
        if (!array_key_exists($key, $this->productSearchApis)) {
            $this->productSearchApis[$key] = self::$container->get(ProductSearchApiFactory::class)->factor($project);
        }

        return $this->productSearchApis[$key];
    }

    protected function queryProductsWithProductSearchApi(
        Project $project,
        string $language,
        array $queryParameters = [],
        ?int $limit = null,
        ?int $offset = null,
        ?string $cursor = null
    ): Result {
        $query = new ProductQuery(
            array_merge(
                $this->buildQueryParameters($language, $limit, $offset, $cursor),
                $queryParameters
            )
        );
        $result = $this
            ->getProductSearchApiForProject($project)
            ->query($query)
            ->wait();

        $this->assertEquals($query, $result->query);
        $this->assertNotSame($query, $result->query);

        return $result;
    }

    /**
     * @return Category[]
     */
    protected function fetchCategories(
        Project $project,
        string $language,
        ?int $limit = null,
        ?int $offset = null,
        ?string $cursor = null
    ): array {
        return $this
            ->getProductApiForProject($project)
            ->getCategories(new CategoryQuery($this->buildQueryParameters($language, $limit, $offset, $cursor)));
    }

    /**
     * @return Result
     */
    protected function queryCategories(
        Project $project,
        string $language,
        ?int $limit = null,
        ?int $offset = null,
        ?string $cursor = null
    ): object {
        return $this
            ->getProductApiForProject($project)
            ->queryCategories(new CategoryQuery($this->buildQueryParameters(
                $language,
                $limit,
                $offset,
                $cursor
            )));
    }

    /**
     * @return Category[]
     */
    protected function fetchAllCategories(Project $project, string $language): array
    {
        $categories = [];

        $limit = 50;
        $cursor = 0;
        do {
            $categoriesFromCurrentStep = $this->fetchCategories($project, $language, $limit, $cursor);
            $categories = array_merge($categories, $categoriesFromCurrentStep);

            $cursor += $limit;
        } while (count($categoriesFromCurrentStep) === $limit);

        return $categories;
    }

    /**
     * @return Category[]
     */
    protected function queryAllCategoriesWithCursor(Project $project, string $language): array
    {
        $categories = [];

        $limit = 50;
        $cursor = null;
        do {
            $resultFromCurrentStep = $this->queryCategories($project, $language, $limit, $cursor);
            $categories = array_merge($categories, $resultFromCurrentStep->items);

            $cursor = $resultFromCurrentStep->nextCursor;
        } while ($resultFromCurrentStep->nextCursor !== null);

        return $categories;
    }

    protected function getAProductWithProductSearchApi(Project $project, string $language): Product
    {
        $result = $this->queryProductsWithProductSearchApi($project, $language);
        $this->assertNotEmpty($result->items);

        return $result->items[0];
    }

    protected function getAProduct(Project $project, string $language): Product
    {
        $result = $this->queryProducts($project, $language);
        $this->assertNotEmpty($result->items);

        return $result->items[0];
    }

    protected function getCartApiForProject(Project $project): CartApi
    {
        return self::$container
            ->get(CartApiFactory::class)
            ->factor($project);
    }

    protected function getAccountApiForProject(Project $project): AccountApi
    {
        return self::$container
            ->get(AccountApiFactory::class)
            ->factor($project);
    }

    protected function getContentApiForProject(Project $project): ContentApi
    {
        return self::$container
            ->get(DefaultContentApiFactory::class)
            ->factor($project);
    }

    protected function buildQueryParameters(
        string $language,
        ?int $limit = null,
        ?int $offset = null,
        ?string $cursor = null
    )
    {
        $parameters = [
            'locale' => $language,
        ];

        if ($limit !== null) {
            $parameters['limit'] = $limit;
        }
        if ($offset !== null) {
            $parameters['offset'] = $offset;
        }
        if ($cursor !== null) {
            $parameters['cursor'] = $cursor;
        }

        return $parameters;
    }

    protected function hasProjectFeature(Project $project, string $featureName): bool
    {
        return $project->configuration['test']->{$featureName} ?? true;
    }

    protected function requireProjectFeature(Project $project, string $featureName): void
    {
        if (!$this->hasProjectFeature($project, $featureName)) {
            $this->markTestSkipped($featureName . ' is required for this test');
        }
    }
}
