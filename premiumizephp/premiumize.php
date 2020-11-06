<?php

use GuzzleHttp\Client;

class Premiumize
{

    private $client;
    private $api_key;

    function __construct($api_key)
    {
        //setup guzzle
        $this->api_key = $api_key;
        $this->client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'https://www.premiumize.me/api/',
            // You can set any number of default request options.
            'timeout'  => 25.0,
            'request.options' => [
                'query' => [
                    'apikey' => $this->api_key
                ]
            ]
        ]);
    }
    public function add($file_path)
    {
        $ext = pathinfo($file_path, PATHINFO_EXTENSION);

        if ($ext === 'magnet') {
            //.magnets files are unkown for premiumize we have to upload them using their link argument api
            $result = $this->client->post('transfer/create', [
                'query' => [
                    'apikey' => $this->api_key
                ],
                'multipart' => [
                    [
                        'name' => 'folder_id',
                        'contents' =>  getenv('DOWNLOAD_FOLDER')
                    ],
                    [
                        'name' => 'src',
                        'contents' => file_get_contents($file_path),
                    ]
                ]
            ]);
        } else {
            $result = $this->client->post('transfer/create', [
                'query' => [
                    'apikey' => $this->api_key
                ],
                'multipart' => [
                    [
                        'name' => 'folder_id',
                        'contents' =>  getenv('DOWNLOAD_FOLDER')
                    ],
                    [
                        'name' => 'file',
                        'contents' => fopen($file_path, 'r'),
                        'filename' => basename($file_path)
                    ]
                ]
            ]);
        }
    }
}
