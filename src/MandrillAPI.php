<?php

namespace evandroaugusto\Mandrill;

use evandroaugusto\HttpClient\HttpClient;


class MandrillAPI
{
    private $apiKey;
    private $endpoint = 'https://mandrillapp.com/api/1.0/messages/%s.json';
    private $client;

    // availables api
    private static $apis = [
        'send' => array(
            'message'
        ),
        'send-template' => array(
            'template_name',
            'template_content',
            'message'
        )
    ];

    /**
     * Initialize class
     */
    public function __construct($apiKey, $client = false)
    {
        $this->setApiKey($apiKey);
        $this->setClient($client);
    }

    /**
     * Call api with json and curl
     */
    public function call($api, $params)
    {
        $this->validateCall($api, $params);

        // Post configuration
        $postUrl    = sprintf($this->endpoint, $api);
        $postHeader = array('Content-Type: application/json');

        // Data
        $data = array('key' => $this->getApiKey());
        $data = $data + $params;
        $jsonData = json_encode($data);

        $options = array(
            'header' => $postHeader,
            'fields' => $jsonData
        );

        return $this->client->post($postUrl, $options);
    }

    /**
     * Validate parameters before call
     */
    protected function validateCall($api, $params)
    {
        if (!$api) {
            throw new \Exception("Empty API specified", 1);
        }

        if (!isset(self::$apis[$api])) {
            throw new \Exception("Invalid API specified", 1);
        }

        if (!$params) {
            throw new \Exception("API parameters not specified", 1);
        }
                
        // validate parameters values
        $required = self::$apis[$api];
        $params   = array_keys($params);

        $missingParams = array_diff($required, $params);

        if ($missingParams) {
            throw new \Exception("Missing parameters: " . implode(',', $missingParams));
        }
    }

    /**
     * Getters and Setters
     */
    protected function setApiKey($apiKey)
    {
        if (!$apiKey) {
            throw new \Exception("API Key not specified", 1);
        }
                      
        $this->apiKey = $apiKey;
    }

    protected function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set http client
     * @param class $client
     */
    protected function setClient($client)
    {
        if ($client) {
            $this->client = $client;
        } else {
            $this->client = new HttpClient();
        }
    }
}
