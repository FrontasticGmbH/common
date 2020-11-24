<?php

namespace Frontastic\Common\SprykerBundle\Domain\Cart\SprykerCart;

use Frontastic\Common\CartApiBundle\Domain\Cart;
use Frontastic\Common\CartApiBundle\Domain\LineItem\Variant;
use Frontastic\Common\SprykerBundle\BaseApi\CartExpandingTrait;
use Frontastic\Common\SprykerBundle\BaseApi\SprykerApiBase;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\Cart\Request\CartItemRequestDataInterface;
use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;

abstract class AbstractSprykerCart extends SprykerApiBase implements SprykerCartInterface
{
    use CartExpandingTrait;

    protected const CART_MAPPER_NAME = 'provide the mapper extending this';

    /**
     * @var AccountHelper
     */
    protected $accountHelper;

    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        LocaleCreator $localeCreator,
        AccountHelper $accountHelper,
        ?string $defaultLanguage = null
    ) {
        parent::__construct($client, $mapperResolver, $localeCreator, $defaultLanguage);
        $this->accountHelper = $accountHelper;
    }

    /**
     * @param string $url
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem\Variant $lineItem
     * @param int|null $count
     * @param string $restMethod
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function lineItemAction(
        string $url,
        Variant $lineItem,
        ?int $count = null,
        string $restMethod = 'post'
    ): Cart {
        $request = $this->createCartItemRequestData($lineItem, $count);

        $response = $this->client->{$restMethod}(
            $url,
            $this->getAuthHeader(),
            $request->encode()
        );

        return $this->mapResponseToCart($response);
    }

    /**
     * @return array
     */
    protected function getAuthHeader(): array
    {
        if ($this->accountHelper->isLoggedIn()) {
            return $this->accountHelper->getAuthHeader();
        }

        return $this->accountHelper->getAnonymousHeader();
    }

    /**
     * @param \WoohooLabs\Yang\JsonApi\Response\JsonApiResponse $response
     *
     * @return \Frontastic\Common\CartApiBundle\Domain\Cart
     */
    protected function mapResponseToCart(JsonApiResponse $response): Cart
    {
        $cart = $this->mapResponseResource($response, static::CART_MAPPER_NAME);
        $this->expandCart($cart, $response->document()->includedResources());

        return $cart;
    }

    /**
     * @param \Frontastic\Common\CartApiBundle\Domain\LineItem\Variant $lineItem
     * @param int|null $count
     *
     * @return \Frontastic\Common\SprykerBundle\Domain\Cart\Request\CartItemRequestDataInterface
     */
    abstract protected function createCartItemRequestData(
        Variant $lineItem,
        ?int $count = null
    ): CartItemRequestDataInterface;
}
