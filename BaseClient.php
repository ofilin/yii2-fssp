<?php

namespace ofilin\fssp;

use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\httpclient\Client;

/**
 * @property \yii\httpclient\Client $client
 */
class BaseClient extends Component
{
    const API_BASE_URL = 'https://api-ip.fssp.gov.ru/api/v1.0';

    public $token;

    private $_client;

    public function init()
    {
        parent::init();

        if ($this->token === null) {
            throw new InvalidConfigException('Token property must be set');
        }
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        if ($this->_client) {
            return $this->_client;
        }

        return new Client([
            'baseUrl' => self::API_BASE_URL,
            'requestConfig' => [
                'format' => Client::FORMAT_JSON
            ],
            'responseConfig' => [
                'format' => Client::FORMAT_JSON
            ],
        ]);
    }

    /**
     * @param $method
     * @param array|null $data
     * @param string $http_method
     * @return mixed
     * @throws InvalidConfigException
     */
    public function call($method, array $data = null, $http_method = 'GET')
    {
        $response = $this->getClient()->createRequest()
            ->setMethod($http_method)
            ->setUrl($method)
            ->setData(array_merge($data, ['token' => $this->token]))
            ->send();

        return $response->data;
    }
}
