<?php

namespace App;

use GuzzleHttp\Client;

class Hail
{
    const BASE_URL = 'https://hail.to/api/v1';

    protected readonly string $clientId;
    protected readonly string $clientSecret;
    protected readonly string $redirectUri;

    public function __construct()
    {
        $this->clientId     = config('services.hail.client_id');
        $this->clientSecret = config('services.hail.client_secret');
        $this->redirectUri  = config('services.hail.redirect_uri');
    }

    public function authorise()
    {
        $provider = $this->getProvider();

        return $provider->authorize(['scope' => 'content.read']);
    }

    public function getAccessToken(string $code)
    {
        $provider = $this->getProvider();

        $response = $provider->getAccessToken('authorization_code', ['grant_type' => 'authorization_code', 'code' => $code]);

        $this->storeToken($response->getToken());
    }

    public function getArticles(string $organisation)
    {
        $provider = $this->getProvider();

        $client = new Client;

        return json_decode($client->send($provider->getAuthenticatedRequest('GET', self::BASE_URL."/organisations/{$organisation}/articles", $this->getToken()))->getBody());
    }

    public function isAuthorised(): bool
    {
        return cache()->has('hail_token');
    }

    protected function storeToken(string $token): void
    {
        cache()->put('hail_token', $token, 3600);
    }

    protected function getToken(): string
    {
        return cache()->get('hail_token') ?? '';
    }

    protected function getProvider(): HailOauth
    {
        return new HailOauth([
            'clientId'      => $this->clientId,
            'clientSecret'  => $this->clientSecret,
            'redirectUri'   => $this->redirectUri
        ]);
    }
}