<?php

namespace Tests\Unit;

use Mockery;
use App\HailOauth;
use Tests\TestCase;
use ReflectionClass;
use League\OAuth2\Client\Token\AccessToken;

class HailOauthTest extends TestCase
{
    protected $provider;

    protected function setUp(): void
    {
        $this->provider = new HailOauth([
            'clientId'      => 'mock_client_id',
            'clientSecret'  => 'mock_secret',
            'redirectUri'   => 'mock_redirect_uri',
        ]);

        parent::setUp();
    }

    public function tearDown(): void
    {
        \Mockery::close();
        parent::tearDown();
    }

    public function testAuthorizationUrl()
    {
        $keys = ['client_id', 'redirect_uri', 'state', 'scope'];
        $url = $this->provider->getAuthorizationUrl();
        $uri = parse_url($url);
        parse_str($uri['query'], $query);
        foreach ($keys as $key) {
            $this->assertArrayHasKey($key, $query);
        }
        $this->assertNotNull($this->provider->getState());
    }

    public function testGetAuthorizationUrl()
    {
        $uri = parse_url($this->provider->getAuthorizationUrl());
        $this->assertEquals('/oauth/authorise', $uri['path']);
    }

    public function testGetBaseAccessTokenUrl()
    {
        $uri = parse_url($this->provider->getBaseAccessTokenUrl([]));
        $this->assertEquals('/api/v1/oauth/access_token', $uri['path']);
    }

    public function testGetAccessToken()
    {
        $testResponse = [
            'access_token'  => 'abcde12345',
            'token_type'    => 'bearer',
            'expires_in'    => 3600,
            'refresh_token' => 'refresh_token',
            'scope'         => 'content.read'
        ];
        $response = Mockery::mock('Psr\Http\Message\ResponseInterface');
        $response->shouldReceive('getBody')->andReturn(json_encode($testResponse));
        $response->shouldReceive('getHeader')->andReturn(['content-type' => 'json']);
        $response->shouldReceive('getStatusCode')->andReturn(200);

        $client = Mockery::mock('GuzzleHttp\ClientInterface');
        $client->shouldReceive('send')->times(1)->andReturn($response);
        $this->provider->setHttpClient($client);

        $token = $this->provider->getAccessToken('authorization_code', ['code' => 'mock_authorization_code']);

        $this->assertEquals($testResponse['access_token'], $token->getToken());
        $this->assertEquals(time() + $testResponse['expires_in'], $token->getExpires());
        $this->assertEquals($testResponse['refresh_token'], $token->getRefreshToken());
    }
}