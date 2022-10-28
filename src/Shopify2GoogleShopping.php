<?php

namespace incapption\Shopify2GoogleShopping;

class Shopify2GoogleShopping {

    public function __construct()
    {

    }

    public function addPair(string $shopifyMetaField, $output, bool $required = true): Shopify2GoogleShopping
    {
        return $this;
    }

    public function generate(): Shopify2GoogleShopping
    {
        return $this;
    }
}