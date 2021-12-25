<?php

if (empty($_GET)) {
    return;
}

require 'vendor/autoload.php';

# https://github.com/cryptapi/php-cryptapi
$payment_data = CryptAPI\CryptAPI::process_callback($_GET);