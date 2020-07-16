<?php

namespace Frontastic\Common\SprykerBundle\Domain\Wishlist;

use Frontastic\Common\SprykerBundle\Domain\Locale\LocaleCreator;
use Frontastic\Common\WishlistApiBundle\Domain\LineItem;
use Frontastic\Common\WishlistApiBundle\Domain\Wishlist;
use Frontastic\Common\WishlistApiBundle\Domain\WishlistApi;
use Frontastic\Common\SprykerBundle\BaseApi\SprykerApiBase;
use Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper;
use Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface;
use Frontastic\Common\SprykerBundle\Domain\MapperResolver;
use Frontastic\Common\SprykerBundle\Domain\Wishlist\Expander\WishlistExpanderInterface;
use Frontastic\Common\SprykerBundle\Domain\Wishlist\Mapper\WishlistMapper;
use Frontastic\Common\SprykerBundle\Domain\Wishlist\Request\WishlistItemsRequestData;
use Frontastic\Common\SprykerBundle\Domain\Wishlist\Request\WishlistRequestData;
use WoohooLabs\Yang\JsonApi\Response\JsonApiResponse;

class SprykerWishlistApi extends SprykerApiBase implements WishlistApi
{
    /**
     * @var AccountHelper
     */
    private $accountHelper;
    /**
     * @var array
     */
    private $includes;

    /**
     * @var \Frontastic\Common\SprykerBundle\Domain\Wishlist\Expander\WishlistExpanderInterface[]
     */
    private $expanders = [];

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\SprykerClientInterface $client
     * @param \Frontastic\Common\SprykerBundle\Domain\MapperResolver $mapperResolver
     * @param \Frontastic\Common\SprykerBundle\Domain\Account\AccountHelper $accountHelper
     * @param LocaleCreator $localeCreator
     * @param array $includes
     */
    public function __construct(
        SprykerClientInterface $client,
        MapperResolver $mapperResolver,
        AccountHelper $accountHelper,
        LocaleCreator $localeCreator,
        array $includes = WishlistConstants::RESOURCES_MAIN
    ) {
        parent::__construct($client, $mapperResolver, $localeCreator);
        $this->accountHelper = $accountHelper;
        $this->includes = $includes;
    }

    /**
     * @param \Frontastic\Common\SprykerBundle\Domain\Wishlist\Expander\WishlistExpanderInterface $expander
     *
     * @return \Frontastic\Common\SprykerBundle\Domain\Wishlist\SprykerWishlistApi
     */
    public function registerExpander(WishlistExpanderInterface $expander): self
    {
        $this->expanders[] = $expander;

        return $this;
    }

    /**
     * @param string $wishlistId
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function getWishlist(string $wishlistId, string $locale): Wishlist
    {
        $response = $this->client->get(
            $this->withIncludes("/wishlists/{$wishlistId}", $this->includes),
            $this->getAuthHeader()
        );

        return $this->mapWishlist($response);
    }

    /**
     * @param string $anonymousId
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function getAnonymous(string $anonymousId, string $locale): Wishlist
    {
        return new Wishlist();
    }

    /**
     * @param string $accountId
     * @param string $locale
     * @return array
     */
    public function getWishlists(string $accountId, string $locale): array
    {
        $response = $this->client->get('/wishlists', $this->getAuthHeader());
        $mappedWishlists = $this->mapWishlistArray($response->document()->primaryResources());
        $wishlists = [];

        foreach ($mappedWishlists as $mappedWishlist) {
            $wishlists[] = $this->getWishlist($mappedWishlist->wishlistId, $locale);
        }

        return $wishlists;
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function create(Wishlist $wishlist, string $locale): Wishlist
    {
        $request = new WishlistRequestData($wishlist->name['de']);

        $response = $this->client->post(
            '/wishlists',
            $this->getAuthHeader(),
            $request->encode()
        );

        return $this->mapWishlist($response);
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem\Variant $lineItem
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function addToWishlist(Wishlist $wishlist, LineItem $lineItem, string $locale): Wishlist
    {
        $request = new WishlistItemsRequestData($lineItem->variant->sku);

        $this->client->post(
            sprintf('/wishlists/%s/wishlist-items', $wishlist->wishlistId),
            $this->getAuthHeader(),
            $request->encode()
        );

        return $this->getWishlist($wishlist->wishlistId, $locale);
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem $lineItem
     * @param int $count
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function updateLineItem(Wishlist $wishlist, LineItem $lineItem, int $count, string $locale): Wishlist
    {
        // Spryker don't have this functionality.
        return $wishlist;
    }

    /**
     * @param \Frontastic\Common\WishlistApiBundle\Domain\Wishlist $wishlist
     * @param \Frontastic\Common\WishlistApiBundle\Domain\LineItem\Variant $lineItem
     * @param string $locale
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    public function removeLineItem(Wishlist $wishlist, LineItem $lineItem, string $locale): Wishlist
    {
        $this->client->delete(
            sprintf(
                '/wishlists/%s/wishlist-items/%s',
                $wishlist->wishlistId,
                $lineItem->variant->sku
            ),
            $this->getAuthHeader()
        );

        return $this->getWishlist($wishlist->wishlistId, $locale);
    }

    /**
     * @return SprykerClientInterface
     */
    public function getDangerousInnerClient()
    {
        return $this->client;
    }

    public function addMultipleToWishlist(Wishlist $wishlist, array $lineItems, string $locale): Wishlist
    {
        // toDo
    }

    /**
     * @return array
     */
    private function getAuthHeader(): array
    {
        return $this->accountHelper->getAuthHeader();
    }

    /**
     * @param \WoohooLabs\Yang\JsonApi\Response\JsonApiResponse $response
     *
     * @return \Frontastic\Common\WishlistApiBundle\Domain\Wishlist
     */
    private function mapWishlist(JsonApiResponse $response): Wishlist
    {
        $document = $response->document();
        $resource = $document->isSingleResourceDocument()
            ? $document->primaryResource()
            : $document->hasAnyPrimaryResources()[0];
        $wishlist = $this->mapperResolver
            ->getMapper(WishlistMapper::MAPPER_NAME)
            ->mapResource($resource);

        foreach ($this->expanders as $expander) {
            $expander->expand($wishlist, $document->includedResources());
        }

        return $wishlist;
    }

    /**
     * @param array $primaryResources
     * @return array
     */
    private function mapWishlistArray(array $primaryResources): array
    {
        return $this->mapperResolver
            ->getExtendedMapper(WishlistMapper::MAPPER_NAME)
            ->mapResourceArray($primaryResources);
    }
}
