<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Telegram extends API_Controller
{
    private $token;
    private $chat_id;


    public function __construct()
    {
        parent::__construct();

        $this->token = '8946416109:AAG7w6LPb24pA_-0SzMcYl46hp2lkanYtGw';
        // $this->chat_id = '1706906331'; // id hafidz
        $this->chat_id = '-5232747334'; // id group hris
    }

    // public function test()
    // {
    //     $result = $this->telegram_lib->sendMessages('HRIS GMP');

    //     echo json_encode($result);
    // }

    // public function sendMessages()
    // {
    //     $pesan = $this->input->get('pesan');

    //     if (empty($pesan)) {
    //         $pesan = 'Testing Telegram dari HRIS';
    //     }

    //     $result = $this->sendMessage($this->chat_id, $pesan);

    //     header('Content-Type: application/json');
    //     echo json_encode($result);
    // }

    // private function sendMessage($chat_id, $message)
    // {
    //     $url = "https://api.telegram.org/bot{$this->token}/sendMessage";

    //     $payload = [
    //         'chat_id' => $chat_id,
    //         'text'    => $message
    //     ];

    //     $ch = curl_init();

    //     curl_setopt_array($ch, [
    //         CURLOPT_URL            => $url,
    //         CURLOPT_POST           => true,
    //         CURLOPT_POSTFIELDS     => $payload,
    //         CURLOPT_RETURNTRANSFER => true
    //     ]);

    //     $response = curl_exec($ch);
    //     $error    = curl_error($ch);

    //     curl_close($ch);

    //     if ($error) {
    //         return [
    //             'status' => false,
    //             'message' => $error
    //         ];
    //     }

    //     return json_decode($response, true);
    // }
}