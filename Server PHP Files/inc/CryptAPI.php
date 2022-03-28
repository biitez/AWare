<?php

class CryptAPI 
{
    private $BaseUrl = 'https://api.cryptapi.io';

    public function GenerateAddress($Callback) 
    {
        try
        {
            $httpStringResponse = @file_get_contents($this->BaseUrl . '/btc/create?' . http_build_query([
                'callback' => $Callback,
                'address' => 'bc1qm8f04zx0npmskp3wqgxpr8dfv0d5yer5kvnl4g',
                'pending' => '0',
                'confirmations' => '1',
                'priority' => 'fast'
            ]));

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
