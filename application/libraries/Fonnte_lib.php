<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Fonnte_lib
{
    private $token;
    private $endpoint = 'https://api.fonnte.com/send';

    public function __construct()
    {
        $this->token = '79fxiLqp3cNLLEsrteHn';
    }

    /**
     * Kirim pesan WhatsApp
     *
     * @param string $target
     * @param string $message
     * @param array  $options
     * @return array
     */
    public function send($target, $message, $options = [])
    {
        $data = [
            'target'      => (string) $target,
            'message'     => $message,
            'countryCode' => isset($options['countryCode'])
                ? (string) $options['countryCode']
                : '62'
        ];

        // Optional parameters
        $optional = [
            'url',
            'filename',
            'schedule',
            'delay',
            'typing',
            'location',
            'followup',
            'connectOnly',
            'sequence',
            'preview',
            'inboxid',
            'duration',
            'choices',
            'select',
            'pollname'
        ];

        foreach ($optional as $key) {
            if (isset($options[$key])) {
                $data[$key] = $options[$key];
            }
        }

        return $this->request($data);
    }

    public function send_file($target, $file_url, $message = '', $filename = '')
    {
        $data = [
            'target'      => (string) $target,
            'message'     => $message,
            'url'         => $file_url,
            'countryCode' => '62'
        ];

        if (!empty($filename)) {
            $data['filename'] = $filename;
        }

        return $this->request($data);
    }

    public function send_location($target, $latitude, $longitude, $message = '')
    {
        $data = [
            'target'      => (string) $target,
            'message'     => $message,
            'location'    => $latitude . ',' . $longitude,
            'countryCode' => '62'
        ];

        return $this->request($data);
    }

    public function send_poll($target, $message, $choices, $select = 'single', $pollname = '')
    {
        $data = [
            'target'      => (string) $target,
            'message'     => $message,
            'choices'     => is_array($choices)
                ? implode(',', $choices)
                : $choices,
            'select'      => $select,
            'countryCode' => '62'
        ];

        if (!empty($pollname)) {
            $data['pollname'] = $pollname;
        }

        return $this->request($data);
    }


    private function request($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL            => $this->endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING       => '',
            CURLOPT_MAXREDIRS      => 10,
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST  => 'POST',
            CURLOPT_POSTFIELDS     => $data,
            CURLOPT_HTTPHEADER     => [
                'Authorization: ' . $this->token
            ],
        ]);

        $response = curl_exec($curl);
        $error    = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        curl_close($curl);

        if ($error) {
            return [
                'status'     => false,
                'reason'     => $error,
                'http_code'  => $httpCode
            ];
        }

        $result = json_decode($response, true);

        if (!is_array($result)) {
            return [
                'status'     => false,
                'reason'     => 'Response Fonnte tidak valid',
                'response'   => $response,
                'http_code'  => $httpCode
            ];
        }

        $result['http_code'] = $httpCode;

        return $result;
    }
}