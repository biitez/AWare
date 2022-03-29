<?php

class CryptAPI 
{
    private $BaseUrl = 'https://api.cryptapi.io';

    public function GenerateAddress($Callback, $BTCAddress) 
    {
        try
        {
            $CryptApiUrl = $this->BaseUrl . '/btc/create?' . http_build_query([
                'callback' => $Callback,
                'address' => $BTCAddress,
                'confirmations' => '1',
                'priority' => 'fast'
            ]);

            $httpStringResponse = @file_get_contents($CryptApiUrl);

            die($httpStringResponse);

            preg_match('/([0-9])\d+/',$http_response_header[0],$matches);

            $ResponseCode = intval($matches[0]);

            if ($ResponseCode >= 400)
            {
                return null;
            }

            $httpJsonResponse = json_decode($httpStringResponse);

            return !isset($httpJsonResponse->status) || $httpJsonResponse->status !== 'success'
                ? null
                : $httpJsonResponse->address_in;
        }
        catch (Exception)
        {
            return null;
        }
    }
}
