<?php

namespace Hyber;

use Http\Client\HttpClient;
use Http\Message\RequestFactory;

class ApiClient
{
    /**
     * @var HttpClient
     */
    private $httpClient;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @param HttpClient $httpClient
     * @param RequestFactory $requestFactory
     */
    public function __construct(HttpClient $httpClient, RequestFactory $requestFactory)
    {
        $this->httpClient = $httpClient;
        $this->requestFactory = $requestFactory;
    }

    /**
     * @param $uri
     * @param $payload
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function apiCall($uri, $payload)
    {
        $request = $this->requestFactory->createRequest('POST', $uri, ['content-type' => 'application/json'], $payload);
        
        return $this->httpClient->sendRequest($request);
    }
}
