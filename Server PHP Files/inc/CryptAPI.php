<?php

namespace CryptAPI;
use Exception;

class CryptAPI {
    private static $base_url = "https://api.cryptapi.io";
    private $valid_coins = [];
    private $own_address = null;
    private $payment_address = null;
    private $callback_url = null;
    private $coin = null;
    private $ca_params = [];
    private $parameters = [];

    public static $COIN_MULTIPLIERS = [
        'btc' => 10**8,
        'bch' => 10**8,
        'ltc' => 10**8,
        'eth' => 10**18,
        'iota' => 10**6,
        'xmr' => 10**12,
        'trx' => 10**6,
    ];

    public function __construct($coin, $own_address, $callback_url, $parameters=[], $ca_params=[]) {
        $this->valid_coins = CryptAPI::get_supported_coins();

        if (!in_array($coin, $this->valid_coins)) {
            $vc = print_r($this->valid_coins, true);
            throw new Exception("Unsupported Coin: {$coin}, Valid options are: {$vc}");
        }

        $this->own_address = $own_address;
        $this->callback_url = $callback_url;
        $this->coin = $coin;
        $this->ca_params = $ca_params;
        $this->parameters = $parameters;
    }

    public static function get_supported_coins() {
        $info = CryptAPI::get_info(null, true);

        if (empty($info)) {
            return null;
        }

        unset($info['fee_tiers']);
    
        $coins = [];

        foreach ($info as $chain => $data) {
            $is_base_coin = in_array('ticker', array_keys($data));
            if ($is_base_coin) {
                $coins[] = $chain;
                continue;
            }

            $base_ticker = "{$chain}_";
            foreach ($data as $token => $subdata) {
                $coins[] = $base_ticker . $token;
            }
        }

        return $coins;
    }

    public function get_address() {
        if (empty($this->own_address) || empty($this->coin) || empty($this->callback_url)) return null;

        $callback_url = $this->callback_url;
        if (!empty($this->parameters)) {
            $req_parameters = http_build_query($this->parameters);
            $callback_url = "{$this->callback_url}?{$req_parameters}";
        }

        $ca_params = array_merge([
            'callback' => $callback_url,
            'address' => $this->own_address,
        ], $this->ca_params);

        $response = CryptAPI::_request($this->coin, 'create', $ca_params);

        if ($response->status == 'success') {
            $this->payment_address = $response->address_in;
            return $response->address_in;
        }

        return null;
    }

    public function check_logs() {
        if (empty($this->coin) || empty($this->callback_url)) return null;

        $params = [
            'callback' => $this->callback_url,
        ];

        $response = CryptAPI::_request($this->coin, 'logs', $params);

        if ($response->status == 'success') {
            return $response;
        }

        return null;
    }

    public function get_qrcode($value = false, $size = false) {
        if (empty($this->coin)) return null;

        $params = [
            'address' => $this->payment_address,
        ];

        if ($value) $params['value'] = $value;
        if ($size) $params['size'] = $size;

        $response = CryptAPI::_request($this->coin, 'qrcode', $params);

        if ($response->status == 'success') {
            return $response;
        }

        return null;
    }

    public static function get_info($coin = null, $assoc = false) {
        $params = [];

        if (empty($coin)) {
            $params['prices'] = '0';
        }

        $response = CryptAPI::_request($coin, 'info', $params, $assoc);

        if (empty($coin) || $response->status == 'success') {
            return $response;
        }

        return null;
    }

    public static function get_estimate($coin, $addresses = 1, $priority = 'default') {
        $response = CryptAPI::_request($coin, 'estimate', [
            'addresses' => $addresses,
            'priority' => $priority
        ]);

        if ($response->status == 'success') {
            return $response;
        }

        return null;
    }
    
    public static function get_convert($coin, $value, $from) {
        $response = CryptAPI::_request($coin, 'convert', [
            'value' => $value,
            'from' => $from
        ]);

        if ($response->status == 'success') {
            return $response;
        }

        return null;
    }


    public static function process_callback($_get, &$parameters) 
    {
        set_error_handler(function($errstr) {
            if (0 === error_reporting())
                return false;
            
            throw new Exception($errstr);
        });

        $parameters = [];

        try
        {
            $params = [
                'address_in' => $_get['address_in'],
                'address_out' => $_get['address_out'],
                'txid_in' => $_get['txid_in'],
                'txid_out' => isset($_get['txid_out']) ? $_get['txid_out'] : null,
                'confirmations' => $_get['confirmations'],
                'value' => $_get['value'],
                'value_coin' => $_get['value_coin'],
                'value_forwarded' => isset($_get['value_forwarded']) ? $_get['value_forwarded'] : null,
                'value_forwarded_coin' => isset($_get['value_forwarded_coin']) ? $_get['value_forwarded_coin'] : null,
                'coin' => $_get['coin'],
                'pending' => isset($_get['pending']) ? $_get['pending'] : false,
            ];
    
            foreach ($_get as $k => $v) {
                if (isset($params[$k])) continue;
                $params[$k] = $_get[$k];
            }

            $parameters = $params;
            // return $params;
        }
        catch (Exception) 
        {
            return false;
        }
    }

    private static function _request($coin, $endpoint, $params = [], $assoc = false) {
        $base_url = Cryptapi::$base_url;
        $coin = str_replace('_', '/', $coin);

        if (!empty($params)) $data = http_build_query($params);

        if (!empty($coin)) {
            $coin = str_replace('_', '/', $coin);
            $url = "{$base_url}/{$coin}/{$endpoint}/";
        } else {
            $url = "{$base_url}/{$endpoint}/";
        }

        if (!empty($data)) $url .= "?{$data}";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, $assoc);
    }
}
