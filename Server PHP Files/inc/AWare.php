<?php

class AWare
{
    private $DefaultCacheJsonFile = [];

    private $CachePath;

    public function __construct($LogsCacheFile)
    {
        $this->CachePath = $LogsCacheFile;
    }

    public function GenerateVictimInvoice($GET)
    {
        $this->GenerateFileAndDirectoryInCaseDoesNotExist();

        # Here create the order and save it on the cache until has been paid
    }

    private function GenerateFileAndDirectoryInCaseDoesNotExist()
    {
        if (file_exists($this->CachePath))
        {
            return;
        }

        if (!str_contains($this->CachePath, '/'))
        {
            file_put_contents($this->CachePath, json_encode([], JSON_PRETTY_PRINT));
            return;
        }


        $PathSplitted = explode('/', $this->CachePath);


    }
}