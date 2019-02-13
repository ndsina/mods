<?php

namespace GoMage\PostCode\Model\Api;

class Google extends Base
{
    /**
     * URL to get address info
     *
     * @const string
     */
    const POST_CODE_API_URL = "https://maps.googleapis.com/maps/api/geocode/json";

    /**
     * Google API Key
     *
     * @const string
     */
    const CONFIG_GOOGLE_API_KEY = 'gomage_postcode/gomage_postcode_group/google_api_key';

    /**
     * @param $postCode
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    public function getAddress($postCode)
    {
        $result = [
            'city'           => '',
            'country'        => '',
            'country_id'     => '',
            'region_id'      => '',
            'region'         => '',
            'completed'      => false,
            'display_fields' => false
        ];

        $postCode = urlencode(trim($postCode));
        $city = $country = $region = '';
        $allowedCountry = $this->_postCodeHelper->getConfigCountryAllow() ?
        explode(',', $this->_postCodeHelper->getConfigCountryAllow()) : [];

        $parameters = [
            'sensor' => true,
            'address' => $postCode
        ];

        if ($apiKey = trim($this->_postCodeHelper->getConfigValue(static::CONFIG_GOOGLE_API_KEY))) {
            $parameters['key'] = $apiKey;
        }

        /** @var \Magento\Framework\HTTP\ZendClient $client */
        $client = $this->_httpClientFactory->create();
        $client->setUri(self::POST_CODE_API_URL);
        $client->setParameterGet($parameters);
        $client->setMethod(\Zend_Http_Client::GET);

        $responseBody = '';
        try {
            $response = $client->request();
            $responseBodyText = $response->getBody();
            $responseBody = $this->_jsonHelper->jsonDecode($responseBodyText);
        } catch (\Exception $e) {
            if (!$responseBodyText) {
                $responseBodyText = $e->getMessage();
            }
            $this->_logger->critical('Google Maps response error: ' . $responseBodyText);
            throw new $e;
        }

        if ($responseBody['status'] == "OK") {
            if (count($responseBody['results'][0]['address_components']) > 0) {
                foreach ($responseBody['results'][0]['address_components'] as $address_component) {
                    foreach ($address_component['types'] as $type) {
                        if ($type == "locality") {
                            $city = $address_component['long_name'];
                        } elseif ($type == "administrative_area_level_1") {
                            $region = $address_component['short_name'];
                        } elseif ($type == "country") {
                            $country = $address_component['short_name'];
                        }
                    }
                }
            }

            if (in_array($country, $allowedCountry)) {
                $result['completed'] = true;
                $result['city'] = $city;
                $result['country_id'] = $result['country'] = $this->_postCodeHelper->getCountryCode($country);
                $result['region_id'] = $result['region'] = $this->_postCodeHelper->getRegionId($result['country'], $region);
                $result['region_code'] = $region;
            }
        } else {
            $result['display_fields'] = true;
            $this->_logger->critical('Google Maps response error: ' . $responseBodyText);
        }

        $dataObject = $this->_dataObjectFactory->create();
        $dataObject->addData($result);
        return $dataObject;
    }
}