<?php
namespace Frontastic\Common\ProductApiBundle\Domain\ProductApi\Commercetools\Client;

use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

class AccessTokenProvider extends AbstractProvider
{
    const TOKEN_URL = 'https://auth.sphere.io/oauth/token';

    const RESOURCE_OWNER_ID = 'id';

    const RESPONSE_ERROR_KEY = 'error';

    public function getBaseAuthorizationUrl(): string
    {
        return self::TOKEN_URL;
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return self::TOKEN_URL;
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token)
    {
        return null;
    }

    protected function getDefaultScopes()
    {
        return null;
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if (empty($data[self::RESPONSE_ERROR_KEY])) {
            return;
        }

        $error = $data[self::RESPONSE_ERROR_KEY];
        $code = 0;
        throw new IdentityProviderException($error, $code, $data);
    }

    protected function createResourceOwner(array $response, AccessToken $token)
    {
        return new GenericResourceOwner($response, self::RESOURCE_OWNER_ID);
    }

    protected function getAccessTokenRequest(array $params)
    {
        $request = parent::getAccessTokenRequest($params);
        $uri = $request->getUri()
            ->withUserInfo($this->clientId, $this->clientSecret);
        return $request->withUri($uri);
    }
}
