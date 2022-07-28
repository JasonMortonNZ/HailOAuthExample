<?php

namespace App;

use Psr\Http\Message\ResponseInterface;
use League\OAuth2\Client\Token\AccessToken;
use League\OAuth2\Client\Provider\AbstractProvider;
use League\OAuth2\Client\Provider\GenericResourceOwner;
use League\OAuth2\Client\Tool\BearerAuthorizationTrait;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

class HailOauth extends AbstractProvider
{
    use BearerAuthorizationTrait;

    private $responseResourceOwnerId = 'id';

    public function getBaseAuthorizationUrl(): string
    {
        return 'https://hail.to/oauth/authorise';
    }

    public function getBaseAccessTokenUrl(array $params): string
    {
        return 'https://hail.to/api/v1/oauth/access_token';
    }

    public function getResourceOwnerDetailsUrl(AccessToken $token): string
    {
        return 'https://hail.to/api/v1/me?' . http_build_query(['access_token' => $token->getToken()]);
    }

    protected function getDefaultScopes()
    {
        return ['content.read'];
    }

    protected function checkResponse(ResponseInterface $response, $data)
    {
        if ($response->getStatusCode() >= 400) {
            throw new IdentityProviderException(
                $data['error'] ?: $response->getReasonPhrase(),
                $response->getStatusCode(),
                $data
            );
        }
    }

    protected function createResourceOwner(array $response, AccessToken $token): GenericResourceOwner
    {
        return new GenericResourceOwner($response, $this->responseResourceOwnerId);
    }
}