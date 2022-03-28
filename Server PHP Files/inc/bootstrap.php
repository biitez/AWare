<?php

header('Content-Type: application/json');

require_once 'inc/Configuration.php';
require_once 'inc/CryptAPI.php';
require_once 'inc/AWare.php';

function ExitMessage($ArrayMessage, $ResponseCode = 200)
{
    http_response_code($ResponseCode);

    die(json_encode(!is_array($ArrayMessage) ? [ 'error' => $ArrayMessage ] : $ArrayMessage, JSON_PRETTY_PRINT));
}