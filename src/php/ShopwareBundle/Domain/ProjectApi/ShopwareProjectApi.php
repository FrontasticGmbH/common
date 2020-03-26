<?php declare(strict_types = 1);

namespace Frontastic\Common\ShopwareBundle\Domain\ProjectApi;

use Doctrine\Common\Cache\Cache;
use Frontastic\Common\ProjectApiBundle\Domain\Attribute;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectApi;
use Frontastic\Common\ProjectApiBundle\Domain\ProjectConfigApi;
use Frontastic\Common\ShopwareBundle\Domain\Client;
use Frontastic\Common\ShopwareBundle\Domain\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;
use function GuzzleHttp\Promise\settle;

class ShopwareProjectApi implements ProjectApi, ProjectConfigApi
{
    /**
     * @var \Frontastic\Common\ShopwareBundle\Domain\Client
     */
    private $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function getProjectConfig(): array
    {
        $contextPromises = [
            'currencies' => $this->client->get('/currency'),
            'countries' => $this->client->get('/country'),
            'languages' => $this->client->get('/language?associations[locale][]'),
            'salutations' => $this->client->get('/salutation'),
            'payment-methods' => $this->client->get('/payment-method'),
            'shipping-methods' => $this->client->get('/shipping-method'),
        ];

        $result = [];
        foreach (settle($contextPromises)->wait() as $resource => $response) {
            if ($response['state'] === PromiseInterface::FULFILLED) {
                // @TODO: map to some DataObjects
                $result[$resource] = $response['value']['data'];
            } else {
                throw new RequestException($response['error']);
            }
        }

        return $result;
    }

    /**
     * @return Attribute[] Attributes mapped by ID
     */
    public function getSearchableAttributes(): array
    {
        // TODO: implement
        return [];
    }
}
