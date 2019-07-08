<?php

namespace ofilin\fssp;

use yii\base\ErrorException;

class Client extends BaseClient
{
    public $retryTime = 1; // time between requests

    public $tryCount = 20; // max request count

    /**
     * @param array $data
     * @return mixed
     * @throws ErrorException
     * @throws FsspException
     * @throws \yii\base\InvalidConfigException
     */
    public function physical(array $data)
    {
        $result = $this->fetch('/search/physical', $data);
        return $result;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws ErrorException
     * @throws FsspException
     * @throws \yii\base\InvalidConfigException
     */
    public function legal(array $data)
    {
        $result = $this->fetch('/search/legal', $data);
        return $result;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws ErrorException
     * @throws FsspException
     * @throws \yii\base\InvalidConfigException
     */
    public function ip(array $data)
    {
        $result = $this->fetch('/search/ip', $data);
        return $result;
    }

    /**
     * @param array $data
     * @return mixed
     * @throws ErrorException
     * @throws FsspException
     * @throws \yii\base\InvalidConfigException
     */
    public function group(array $data)
    {
        $result = $this->fetch('/search/group', $data, true);
        return $result;
    }

    /**
     * @param $method
     * @param $data
     * @return mixed
     * @throws ErrorException
     * @throws FsspException
     * @throws \yii\base\InvalidConfigException
     */
    public function fetch($method, $data, $isPost = false)
    {
        if ($isPost) {
            $response = $this->call($method, $data, 'POST');
        } else {
            $response = $this->call($method, $data);
        }

        if (isset($response['status']) && $response['status'] == 'error') {
            throw new ErrorException($response['exception']);
        }

        if (!isset($response['response']['task'])) {
            var_dump($response);
            throw new ErrorException('Task id not received');
        }
        $task = $response['response']['task'];

        $i = 0;
        do {
            $i++;
            sleep($this->retryTime);
            $resp_status = $this->call('/status', ['task' => $task]);
            $status = $resp_status["response"]["status"];

            if ($i >= $this->tryCount) {
                throw new ErrorException('Timeout exceeded');
            }
        } while ($status != 0);

        if ($resp_status['code'] != 0) {
            throw new ErrorException('Too many time');
        }

        $response = $this->call('/result', ['task' => $task]);

        if (!isset($response["response"]["result"])) {
            throw new FsspException('Assertion error, unexpected result');
        }

        if (count($response["response"]["result"]) == 1) {
            return $response["response"]["result"]["0"]["result"];
        }

        return $response["response"]["result"];
    }


}
