<?php

/**
 * Metropol
 * @author    James Ngugi <ngugi823@gmail.com>
 * @copyright Copyright (c) James Ngugi
 */

namespace Ngugi\Metropol;

use Exception;

class Metropol
{
    /**
     * @var mixed
     */
    private $publicApiKey;
    /**
     * @var mixed
     */
    private $privateApiKey;

    /**
     * @var mixed
     */
    private $responseBody;

    /**
     * @var mixed
     */
    private $responseInfo;

    /**
     * @var string
     */
    private $baseEndpoint = "https://api.metropol.co.ke";

    /**
     * Always clarify the port with CRB before making any connection
     * @var mixed
     */
    private $port = 443;

    /**
     * @param $argument
     */
    public function __construct($publicApiKey, $privateApiKey)
    {

        $this->publicApiKey  = $publicApiKey;
        $this->privateApiKey = $privateApiKey;
    }

    /**
     * @param $payload
     */
    private function setHeaders($payload)
    {
        //calculate the timestamp as required e.g 2014 07 08 17 58 39 987843
        //Format: Year, Month, Day, Hour, Minute, Second, Milliseconds
        $apiTimestamp = date('Y') . date('m') . date('d') . date('G') . date('i') . date('s') . date('u');

        //calculate the rest api hash as required
        $apiHash = $this->calculateHash($payload, $apiTimestamp);

        return array(
            "X-METROPOL-REST-API-KEY:" . $this->publicApiKey,
            "X-METROPOL-REST-API-HASH:" . $apiHash,
            "X-METROPOL-REST-API-TIMESTAMP:" . $apiTimestamp,
            "Content-Type: application/json",
        );
    }

    /**
     * @param $payload
     * @param $apiTimestamp
     */
    private function calculateHash($payload, $apiTimestamp)
    {
        $string = $this->privateApiKey . $payload . $this->publicApiKey . $apiTimestamp;

        return hash('sha256', $string);
    }

    /**
     * @param  $endpoint
     * @param  $payload    json string
     * @return mixed
     */
    private function httpPost($endpoint, $payload)
    {

        $url = $this->baseEndpoint . ":" . $this->port . $endpoint;

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $this->setHeaders($payload));

        return $this->doExecute($ch);

    }

    /**
     * @param $curlHandle_
     */
    private function doExecute(&$curlHandle_)
    {
        try {

            $this->responseBody = curl_exec($curlHandle_);

            $this->responseInfo = curl_getinfo($curlHandle_);

            curl_close($curlHandle_);

            return $this->responseBody;

        } catch (Exception $e) {
            curl_close($curlHandle_);

            throw $e;
        }
    }

    /**
     * @param $id_number
     * @return mixed
     */
    public function identityVerification($id_number)
    {
        $endpoint = '/identity/verify';

        $payload = json_encode(array(
            "report_type"     => 1,
            "identity_number" => (string) $id_number,
            "identity_type"   => "001",
        ));

        return json_decode($this->httpPost($endpoint, $payload));
    }

    /**
     * @param $id_number
     * @param $loan_amount
     * @return mixed
     */
    public function deliquencyStatus($id_number, $loan_amount)
    {
        $endpoint = '/deliquency/status';

        $payload = json_encode(array(
            "report_type"     => 2,
            "identity_number" => (string) $id_number,
            "identity_type"   => "001",
            "loan_amount"     => $loan_amount,
        ));

        return json_decode($this->httpPost($endpoint, $payload));
    }

    /**
     * @param $id_number
     * @param $loan_amount
     * @return mixed
     */
    public function creditInfo($id_number, $loan_amount)
    {
        $endpoint = '/report/credit_info';

        $payload = json_encode(array(
            "report_type"     => 8,
            "identity_number" => (string) $id_number,
            "identity_type"   => "001",
            "loan_amount"     => $loan_amount,
            "report_reason"   => 1,
        ));

        return json_decode($this->httpPost($endpoint, $payload));
    }

    /**
     * @param $id_number
     * @return mixed
     */
    public function ConsumerScore($id_number)
    {
        $endpoint = '/score/consumer';

        $payload = json_encode(array(
            "report_type"     => 3,
            "identity_number" => (string) $id_number,
            "identity_type"   => "001",
        ));

        return json_decode($this->httpPost($endpoint, $payload));
    }

    /**
     * Gets the value of publicApiKey.
     *
     * @return mixed
     */
    public function getPublicApiKey()
    {
        return $this->publicApiKey;
    }

    /**
     * Sets the value of publicApiKey.
     *
     * @param  mixed  $publicApiKey the public api key
     * @return self
     */
    public function setPublicApiKey($publicApiKey)
    {
        $this->publicApiKey = $publicApiKey;

        return $this;
    }

    /**
     * Gets the value of privateApiKey.
     *
     * @return mixed
     */
    public function getPrivateApiKey()
    {
        return $this->privateApiKey;
    }

    /**
     * Sets the value of privateApiKey.
     *
     * @param  mixed  $privateApiKey the private api key
     * @return self
     */
    public function setPrivateApiKey($privateApiKey)
    {
        $this->privateApiKey = $privateApiKey;

        return $this;
    }

    /**
     * Gets the value of responseBody.
     *
     * @return mixed
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * Sets the value of responseBody.
     *
     * @param  mixed  $responseBody the response body
     * @return self
     */
    public function setResponseBody($responseBody)
    {
        $this->responseBody = $responseBody;

        return $this;
    }

    /**
     * Gets the value of responseInfo.
     *
     * @return mixed
     */
    public function getResponseInfo()
    {
        return $this->responseInfo;
    }

    /**
     * Sets the value of responseInfo.
     *
     * @param  mixed  $responseInfo the response info
     * @return self
     */
    public function setResponseInfo($responseInfo)
    {
        $this->responseInfo = $responseInfo;

        return $this;
    }

    /**
     * Gets the value of baseEndpoint.
     *
     * @return string
     */
    public function getBaseEndpoint()
    {
        return $this->baseEndpoint;
    }

    /**
     * Sets the value of baseEndpoint.
     *
     * @param  string $baseEndpoint the base endpoint
     * @return self
     */
    public function setBaseEndpoint($baseEndpoint)
    {
        $this->baseEndpoint = $baseEndpoint;

        return $this;
    }

    /**
     * Gets the value of port.
     *
     * @return mixed
     */
    public function getPort()
    {
        return $this->port;
    }

    /**
     * Sets the value of port.
     *
     * @param  mixed  $port the port
     * @return self
     */
    public function setPort($port)
    {
        $this->port = $port;

        return $this;
    }
}
