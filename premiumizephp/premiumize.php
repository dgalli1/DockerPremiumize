<?php

use GuzzleHttp\Client;

class Premiumize
{

    private $client;
    private $api_key;
    private $series_folder;

    private $premiumize_cache;

    function __construct($api_key,$load_series_folder)
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
        if($load_series_folder) {
            $this->premiumize_cache = new PremiumizePathCache($this);
        }
    }
    public function getFolder($id,$children_only = true) {
        $result = $this->client->get('folder/list', [
            'query' => [
                'apikey' => $this->api_key,
                'id' => $id
            ]
        ]);
        $body = json_decode($result->getBody(), true);
        if($children_only) {
            return $body['content'];
        }
        return $body;
    }

    public function getFile($id) {
        $result = $this->client->get('item/details', [
            'query' => [
                'apikey' => $this->api_key,
                'id' => $id
            ]
        ]);
        return $result->getBody();
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

    public function resolvePath($path) {
        return $this->premiumize_cache->resolvePath($path);
    }

    /**
     * Get the value of feeds_folder
     */ 
    public function getSeries_folder()
    {
        return $this->series_folder;
    }

    /**
     * Set the value of feeds_folder
     *
     * @return  self
     */ 
    public function setSeries_folder($series_folder)
    {
        $this->series_folder = $series_folder;

        return $this;
    }

}
