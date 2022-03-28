<?php

require_once 'inc/bootstrap.php';

if (isset($_GET['victim']))
{
    $Aware = new AWare(TempCacheJsonPath);
}

if (!CryptAPI\CryptAPI::process_callback($_GET, $Arrays))
{
    ExitMessage('Invalid GET Parameters');
}