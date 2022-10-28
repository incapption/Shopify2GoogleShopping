<?php

namespace Incapption\Shopify2GoogleShopping\Exceptions;

use Exception;

class InvalidMethodException extends Exception
{
    public function __construct($additional_msg = null, $val = 0, Exception $old = null)
    {
        parent::__construct(!empty($additional_msg) ? $additional_msg : 'Invalid method used. Allowed methods are: POST, GET, PUT, DELETE', $val, $old);
    }

    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}