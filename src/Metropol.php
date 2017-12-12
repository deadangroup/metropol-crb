<?php

/**
 * Metropol
 * @author    James Ngugi <ngugi823@gmail.com>
 * @copyright Copyright (c) James Ngugi
 */

namespace Ngugi\Metropol;

use GuzzleHttp\Client;

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
     * @var string
     */
    private $baseEndpoint = "https://api.metropol.co.ke";

    /**
     * Always clarify the port with CRB before making any connection
     * @var mixed
     */
    private $port = 443;

    /**
     * @var Client
     */
    private $http;

    /**
     * The Metropol API version
     * @var string
     */
    private $version = null;

    /**
     * Metropol constructor.
     * @param $publicApiKey
     * @param $privateApiKey
     */
    public function __construct($publicApiKey, $privateApiKey)
    {
        $this->publicApiKey = $publicApiKey;
        $this->privateApiKey = $privateApiKey;

        $this->http = new Client([
            'base_uri'        => $this->baseEndpoint . ":" . $this->port,
            'timeout'         => 60,
            'allow_redirects' => true,
        ]);
    }

    /**
     * @param string $version
     */
    public function withVersion($version)
    {
        $this->version = $version;
    }

    /**
     * @return null|string
     */
    public function getVersion()
    {
        if (!is_null($this->version)) {
            return '/' . $this->version;
        }

        return null;
    }

    /**
     * @param $payload
     */
    private function withHeaders($payload)
    {
        //calculate the timestamp as required e.g 2014 07 08 17 58 39 987843
        //Format: Year, Month, Day, Hour, Minute, Second, Milliseconds
        $apiTimestamp = date('Y') . date('m') . date('d') . date('G') . date('i') . date('s') . date('u');

        //calculate the rest api hash as required
        $apiHash = $this->calculateHash($payload, $apiTimestamp);

        return [
            "X-METROPOL-REST-API-KEY:" . $this->publicApiKey,
            "X-METROPOL-REST-API-HASH:" . $apiHash,
            "X-METROPOL-REST-API-TIMESTAMP:" . $apiTimestamp,
            "Content-Type: application/json",
        ];
    }

    /**
     * @param $publicApiKey
     * @return $this
     */
    public function withPublicApiKey($publicApiKey)
    {
        $this->publicApiKey = $publicApiKey;
        return $this;
    }

    /**
     * @param $privateApiKey
     * @return $this
     */
    public function withPrivateApiKey($privateApiKey)
    {
        $this->privateApiKey = $privateApiKey;
        return $this;
    }

    /**
     * @param $baseEndpoint
     * @return $this
     */
    public function withBaseEndpoint($baseEndpoint)
    {
        $this->baseEndpoint = $baseEndpoint;
        return $this;
    }

    /**
     * @param $port
     * @return $this
     */
    public function withPort($port)
    {
        $this->port = $port;
        return $this;
    }

    /**
     * @param Client $http
     * @return $this
     */
    public function withHttp(Client $http)
    {
        $this->http = $http;
        return $this;
    }

    /**
     * @param $payload
     * @param $apiTimestamp
     * @return string
     */
    private function calculateHash($payload, $apiTimestamp)
    {
        $string = $this->privateApiKey . $payload . $this->publicApiKey . $apiTimestamp;

        return hash('sha256', $string);
    }

    /**
     * @param $endpoint
     * @param $payload
     * @return string
     */
    private function httpPost($endpoint, $payload)
    {
        $url = $this->getVersion() . $endpoint;

        $response = $this->http->request('POST', $url, [
            'form_params' => $payload,
            'headers'     => $this->withHeaders($payload),
            'http_errors' => false //let users handle errors
        ]);

        return $response->getBody()->getContents();
    }

    /**
     * @param $id_number
     * @return array
     */
    public function identityVerification($id_number)
    {
        $endpoint = '/identity/verify';

        $payload = [
            "report_type"     => 1,
            "identity_number" => (string) $id_number,
            "identity_type"   => "001",
        ];

        return json_decode($this->httpPost($endpoint, $payload));
    }

    /**
     * @param $id_number
     * @param $loan_amount
     * @return array
     */
    public function deliquencyStatus($id_number, $loan_amount)
    {
        $endpoint = '/deliquency/status';

        $payload = [
            "report_type"     => 2,
            "identity_number" => (string) $id_number,
            "identity_type"   => "001",
            "loan_amount"     => $loan_amount,
        ];

        return json_decode($this->httpPost($endpoint, $payload));
    }

    /**
     * @param $id_number
     * @param $loan_amount
     * @return array
     */
    public function creditInfo($id_number, $loan_amount)
    {
        $endpoint = '/report/credit_info';

        $payload = [
            "report_type"     => 8,
            "identity_number" => (string) $id_number,
            "identity_type"   => "001",
            "loan_amount"     => $loan_amount,
            "report_reason"   => 1,
        ];

        return json_decode($this->httpPost($endpoint, $payload));
    }

    /**
     * @param $id_number
     * @return array
     */
    public function consumerScore($id_number)
    {
        $endpoint = '/score/consumer';

        $payload = [
            "report_type"     => 3,
            "identity_number" => (string) $id_number,
            "identity_type"   => "001",
        ];

        return json_decode($this->httpPost($endpoint, $payload));
    }
}
