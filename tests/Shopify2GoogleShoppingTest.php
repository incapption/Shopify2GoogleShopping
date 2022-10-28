<?php

namespace Incapption\LoadBalancedCronTask\Tests;

use Incapption\Shopify2GoogleShopping\Shopify2GoogleShopping;
use PHPUnit\Framework\TestCase;

class Shopify2GoogleShoppingTest extends TestCase
{
    /**
     * @var string
     */
    private $testFilePath;

    public function __construct()
    {
        include('vendor/autoload.php');

        $dotenv = \Dotenv\Dotenv::createImmutable(dirname(__FILE__, 2));
        $dotenv->load();

        $this->testFilePath = 'tests/storage/test.xml';

        if (file_exists($this->testFilePath))
        {
            unlink($this->testFilePath);
        }

        parent::__construct();
    }

    /** @test */
    public function generate_a_test_xml_file()
    {
        $client = new Shopify2GoogleShopping();
        $client->setShopifyCredentials($_ENV['SHOPIFY_PRIVATE_API_KEY'], $_ENV['SHOPIFY_PRIVATE_API_PASSWORD'], $_ENV['SHOPIFY_PRIVATE_API_ACCESS_TOKEN'], $_ENV['SHOPIFY_PRIVATE_HOST']);

        $client->setProjectTitle('My Custom Shopify Shop');
        $client->setProjectDescription('This is a short description of my shop');
        $client->setProjectLink('https://myshop.shopify.com');

        file_put_contents($this->testFilePath, $client->generate());
    }
}