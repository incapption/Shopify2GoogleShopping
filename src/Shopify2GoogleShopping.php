<?php

namespace Incapption\Shopify2GoogleShopping;

use Incapption\Shopify2GoogleShopping\Handler\ShopifyHandler;
use Liquid\Template;

class Shopify2GoogleShopping {

    /**
     * @var array
     */
    private $shopifyCredentials;

    /**
     * @var array
     */
    private $project;

    /**
     *
     * @var string
     */
    private $templatePath;

    /**
     *
     * @var int
     */
    private $rateLimiter;

    public function __construct()
    {
        $this->setTemplate(dirname(__FILE__).'/Templates/template.liquid');
        $this->setRateLimiter(1);
    }

    /**
     * Sets the credentials for your private shopify admin api
     *
     * @param  string  $apiKey
     * @param  string  $password
     * @param  string  $access_token
     * @param  string  $host e.g. https://demo.myshopify.com
     * @return $this
     */
    public function setShopifyCredentials(string $apiKey, string $password, string $access_token, string $host): Shopify2GoogleShopping
    {
        $this->shopifyCredentials = [
            'apiKey' => $apiKey,
            'password' => $password,
            'access_token' => $access_token,
            'host' => $host,
        ];

        return $this;
    }

    /**
     * Sets a custom template path. Template must be a liquid file (e.g. custom-template.liquid)
     *
     * @param  string  $templatePath Path to your custom template
     * @return $this
     */
    public function setTemplate(string $templatePath): Shopify2GoogleShopping
    {
        $this->templatePath = $templatePath;

        return $this;
    }

    /**
     * Sets a project title
     *
     * @param  string  $title
     * @return $this
     */
    public function setProjectTitle(string $title): Shopify2GoogleShopping
    {
        $this->project['title'] = $title;

        return $this;
    }

    /**
     * Sets a short description for your shop
     *
     * @param  string  $description
     * @return $this
     */
    public function setProjectDescription(string $description): Shopify2GoogleShopping
    {
        $this->project['description'] = $description;

        return $this;
    }

    /**
     * Sets the link to your shop
     *
     * @param  string  $link
     * @return $this
     */
    public function setProjectLink(string $link): Shopify2GoogleShopping
    {
        $this->project['link'] = $link;

        return $this;
    }

    /**
     * Sets a simple rate limiter for the api
     *
     * @param  int  $requestPerSecond
     * @return void
     */
    public function setRateLimiter(int $requestPerSecond = 1)
    {
        $this->rateLimiter = $requestPerSecond;
    }

    /**
     * Generates the xml file for google shopping
     *
     * @return string
     */
    public function generate(): string
    {
        $items = $this->getProducts();

        foreach ($items as &$item)
        {
            $item['body_html'] = preg_replace('/\xc2\xa0/', '', $item['body_html']);
            $item['body_html'] = preg_replace('/ {2,}/', ' ', trim($item['body_html']));

            foreach ($item['metafields'] as $itemMetafield)
            {
                $item['metafield'][$itemMetafield['namespace']][$itemMetafield['key']] = $itemMetafield['value'];
            }
        }

        $template = new Template();
        $template->parse(file_get_contents($this->templatePath));

        return $template->render([
            'project' => $this->project,
            'items' => $items
        ]);
    }

    /**
     * @return array
     */
    private function getProducts() : array
    {
        $shopifyHandler = new ShopifyHandler($this->shopifyCredentials['apiKey'], $this->shopifyCredentials['password'], $this->shopifyCredentials['access_token'], $this->shopifyCredentials['host']);
        $products = $shopifyHandler->get('/admin/api/2022-10/products.json', [])['products'];

        foreach ($products as &$product)
        {
            usleep((1000000 / $this->rateLimiter));
            $product += $shopifyHandler->get('/admin/api/2022-10/products/'.$product['id'].'/metafields.json', []);
        }

        if(!empty($products))
        {
            return $products;
        }

        return [];
    }
}