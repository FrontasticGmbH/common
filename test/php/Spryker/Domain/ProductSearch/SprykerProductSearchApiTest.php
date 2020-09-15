<?php

namespace Frontastic\Common\SprykerBundle\Domain\ProductSearch;

use Frontastic\Common\SprykerBundle\Domain\Locale\DefaultLocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\Locale\SprykerLocale;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver as SprykerMapperResolver;
use Frontastic\Common\SprykerBundle\Domain\Project\Mapper\ProductSearchableAttributesMapper;
use Frontastic\Common\SprykerBundle\Domain\SprykerClient;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;

class SprykerProductSearchApiTest extends TestCase
{
    /**
     * @var SprykerClient|MockObject
     */
    private $clientMock;

    /**
     * @var DefaultLocaleCreator
     */
    private $localCreatorMock;

    /**
     * @var SprykerProductSearchApi
     */
    private $api;

    public function setup()
    {
        $this->clientMock = $this
            ->getMockBuilder(SprykerClient::class)
            ->disableOriginalConstructor()
            ->getMock();
        $productSearchableAttributesMapper = new ProductSearchableAttributesMapper();
        $this->localCreatorMock = $this->createMock(LocaleCreator::class);
        $this->api = new SprykerProductSearchApi(
            $this->clientMock,
            new SprykerMapperResolver([$productSearchableAttributesMapper]),
            $this->localCreatorMock,
            ['en_GB@GBP']
        );
    }

    public function testSearcheableAttributes()
    {
        $this->clientMock
            ->expects($this->once())
            ->method('get')
            ->will($this->returnTestSearchableAttributes());

        $sprykerLocale = new SprykerLocale([
            'language' => 'en',
            'country' => 'GB',
            'currency' => 'GBP',
        ]);
        $this->localCreatorMock
            ->expects($this->any())
            ->method('createLocaleFromString')
            ->willReturn($sprykerLocale);

        $attributes = $this->api->getSearchableAttributes()->wait();

        $this->assertCount(5, $attributes);

        foreach ($attributes as $attribute) {
            $this->assertNotNull($attribute->attributeId);
            $this->assertNotNull($attribute->type);
        }
    }

    private function returnTestSearchableAttributes()
    {
        $body = file_get_contents( __DIR__  . '/_fixtures/searchableAttributesBodyResponse.json');
        $response = new Response(200, [], $body);

        return $this->returnValue(new JsonApiResponse($response));
    }

}
