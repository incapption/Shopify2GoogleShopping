<?php

namespace Incapption\Shopify2GoogleShopping\Handler;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Incapption\Shopify2GoogleShopping\Exceptions\InvalidMethodException;

class ShopifyHandler
{
    protected $api_key;
    protected $password;
    protected $url;
    protected $host;
    protected $access_token;
    protected $client;

    public function __construct($api_key, $password, $access_token, $host)
    {
        $this->api_key = $api_key;
        $this->password = $password;
        $this->access_token = $access_token;
        $this->host = $host;

        $this->url = "https://{$this->api_key}:{$this->password}@{$this->host}";
        $this->client = new Client(['headers' => ['X-Shopify-Access-Token' => $this->access_token]]);
    }

    /**
     * @throws InvalidMethodException|GuzzleException
     */
    public function __call($method, $args)
    {
        $method = strtoupper($method);
        $allowedMethods = ['POST', 'GET', 'PUT', 'DELETE'];

        if (!in_array($method, $allowedMethods)) {
            throw new InvalidMethodException();
        }

        return $this->request($method, trim($args[0]), $args[1] ?? []);
    }

    /**
     * @throws GuzzleException
     */
    protected function request(string $method, string $uri, array $payload)
    {
        $response = $this->client->request(
            $method,
            "{$this->url}{$uri}",
            [
                'form_params' => $payload
            ]
        );

        return json_decode($response->getBody(), true);
    }

}