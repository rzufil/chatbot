<?php

namespace App\Helpers;

class CurrencyConverstionHelper
{
    public static function exchange($from, $to, $amount)
    {
        $base_url = 'https://www.amdoren.com/api/currency.php';
        $api_key = env('AMDOREN_API_KEY');
        $url = $base_url . "?api_key=" . $api_key . "&from=" . $from . "&to=" . $to . "&amount=" . $amount;
        $ch = curl_init();  
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 15); 
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        $json_string = curl_exec($ch);
        $parsed_json = json_decode($json_string);

        $error = $parsed_json->error;
        $error_message = $parsed_json->error_message;
        $amount = $parsed_json->amount;
        return [
            'error' => $error,
            'error_message' => $error_message,
            'response' => $amount
        ];
    }
}
