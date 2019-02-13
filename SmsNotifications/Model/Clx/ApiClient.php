<?php

namespace GoMage\SmsNotifications\Model\Clx;

use Magento\Framework\Json\Helper\Data as JsonHelper;

class ApiClient
{
    const API_BATCH_CREATED = 201;

    /**
     * @var \Magento\Framework\HTTP\ZendClientFactory
     */
    protected $httpClientFactory;

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * ApiClient constructor.
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
     * @param JsonHelper $jsonHelper
     */
    public function __construct(
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        JsonHelper $jsonHelper
    ) {
        $this->httpClientFactory  = $httpClientFactory;
        $this->jsonHelper         = $jsonHelper;
    }

    /**
     * @param string $url
     * @param string $method
     * @param array $headers
     * @param array $data
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function request(
        $url = '',
        $method = \Zend_Http_Client::GET,
        $headers = [],
        $data = []
    )
    {
        /** @var \Magento\Framework\HTTP\ZendClient $client */
        $client = $this->httpClientFactory->create();
        $client->setHeaders($headers);
        $client->setUri($url);
        if ($data) {
            $client->setRawData($this->jsonHelper->jsonEncode($data));
        }

        $client->setMethod($method);

        $response = $client->request();
        if (!$response->isSuccessful()) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __($response->getMessage())
            );
        }
        $responseBody = $response->getBody();

        return $this->jsonHelper->jsonDecode($responseBody);
    }
}