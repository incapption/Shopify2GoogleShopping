# Shopify2GoogleShopping
This is a package to quickly create a XML file for Google Shopping from Shopify products.

### Installation
```bash
composer require incapption/shopify2googleshopping
```

### How to use it

On the PHP side it looks something like this

```php
<?php

use Incapption\Shopify2GoogleShopping\Shopify2GoogleShopping;

class SomeClass
{
    public function doSomething()
    {
        $handler = new Shopify2GoogleShopping();
        
        // set your shopify admin api credentials
        $handler->setShopifyCredentials(
                    $_ENV['SHOPIFY_PRIVATE_API_KEY'], 
                    $_ENV['SHOPIFY_PRIVATE_API_PASSWORD'], 
                    $_ENV['SHOPIFY_PRIVATE_API_ACCESS_TOKEN'], 
                    "mydemoshop.myshopify.com"
                 );

        // optional: $handler->setTemplate('mypath/custom_template.liquid');

        $handler->setProjectTitle('My Custom Shopify Shop');
        $handler->setProjectDescription('This is a short description of my shop');
        $handler->setProjectLink('https://myshop.shopify.com');

        file_put_contents('mypath/product_feed.xml', $handler->generate());
    }
}
```

The template looks like this. You can either use the existing template or create your own. It has to be a liquid file.

```liquid
<?xml version="1.0" encoding="UTF-8" ?>
<rss version ="2.0" xmlns:g="http://base.google.com/ns/1.0" xmlns:c="http://base.google.com/cns/1.0">
    <channel>
        <title>{{ project.title }}</title>
        <description>{{ project.description }}</description>
        <link>{{ project.link }}</link>

        {% for item in items %}
            <item>
                <g:title>{{ item.title }}</g:title>
                <g:condition>new</g:condition>
                <g:description>{{ item.body_html | strip_html | strip_newlines }}</g:description>
                <g:id>{{ item.id }}</g:id>
                <g:google_product_category>{{ item.metafield.custom.google_product_category }}</g:google_product_category>
                <g:image_link>{{ item.image.src }}</g:image_link>
                <g:link>{{ project.link }}/products/{{ item.handle }}</g:link>
                <g:price>{{ item.variants[0].price | decimals: 2, "en-US" | append: " EUR" }}</g:price>
                <g:availability>in stock</g:availability>
                <g:gtin>{{ item.metafield.custom.gtin }}</g:gtin>
                <g:brand>{{ item.metafield.custom.brand }}</g:brand>
                <g:g:product_type>{{ item.metafield.custom.product_type }}</g:g:product_type>
                <g:adult>no</g:adult>
                <g:shipping>
                    <g:country>Germany</g:country>
                    <g:service>Download</g:service>
                    <g:price>{{ '0.00' | decimals: 2, "en-US" | append: " EUR" }}</g:price>
                </g:shipping>
            </item>
        {% endfor %}

    </channel>
</rss>
```

### Testing
You can test this package. For this you need your Shopify credentials. Make a copy named .env of the .env-example and enter your data.
```bash
./vendor/bin/phpunit tests
```