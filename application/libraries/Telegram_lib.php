<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Telegram_lib
{
    private $CI;
    private $token;

    public function __construct()
    {
        $this->CI =& get_instance();

        $this->token = '8946416109:AAG7w6LPb24pA_-0SzMcYl46hp2lkanYtGw';
        // $this->chat_id = '-5232747334'; // id group hris

        $this->chat_id = '-1004319679928';
    }

    public function sendMessages($message)
    {
        $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

        $payload = [
            'chat_id' => $this->chat_id,
            'text'    => $message
        ];

        $ch = curl_init();

        curl_setopt_array($ch, [
            CURLOPT_URL            => $url,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $payload,
            CURLOPT_RETURNTRANSFER => true
        ]);

        $response = curl_exec($ch);

        curl_close($ch);

        $result = json_decode($response, true);

        // Jika group berubah menjadi supergroup
        if (
            isset($result['parameters']['migrate_to_chat_id'])
        ) {
            $this->chat_id = $result['parameters']['migrate_to_chat_id'];

            // Kirim ulang dengan chat_id baru
            $payload['chat_id'] = $this->chat_id;

            $ch = curl_init();

            curl_setopt_array($ch, [
                CURLOPT_URL            => $url,
                CURLOPT_POST           => true,
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_RETURNTRANSFER => true
            ]);

            $response = curl_exec($ch);

            curl_close($ch);

            $result = json_decode($response, true);
        }

        return $result;
    }
}