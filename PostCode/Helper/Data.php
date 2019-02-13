<?php

namespace GoMage\PostCode\Helper;

use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Directory\Model\ResourceModel\Country\Collection as CountryCollection;
use Magento\Directory\Model\ResourceModel\Region\Collection as RegionCollection;
use GoMage\PostCode\Model\PostCodeFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use GoMage\PostCode\Model\Source\Mode as SourceMode;
use GoMage\PostCode\Model\Api\Factory as ModeApiFactory;
use Magento\Framework\DataObjectFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Config API mode
     *
     * @const string
     */
    const CONFIG_API_MODE = 'gomage_postcode/gomage_postcode_group/api_mode';

    /**
     * ZIP caching
     *
     * @const string
     */
    const CONFIG_ENABLED_ZIP_CACHING = 'gomage_postcode/gomage_postcode_group/enabled_zip_caching';

    /**
     * Country allow
     *
     * @const string
     */
    const CONFIG_COUNTRY_ALLOW = 'general/country/allow';

    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * Country collection
     *
     * @var \Magento\Directory\Model\ResourceModel\Country\Collection
     */
    protected $_countryCollection;

    /**
     * Region collection
     *
     * @var \Magento\Directory\Model\ResourceModel\Region\Collection
     */
    protected $_regionCollection;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var \GoMage\PostCode\Model\PostCodeFactory
     */
    protected $_postCodeFactory;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $_logger;

    /**
     * @var ApiFactory
     */
    protected $_apiFactory;

    /**
     * Data constructor.
     * @param CountryCollection $countryCollection
     * @param RegionCollection $regionCollection
     * @param JsonHelper $jsonHelper
     * @param PostCodeFactory $postCodeFactory
     * @param ScopeConfigInterface $_scopeConfig
     * @param SourceMode $sourceMode
     * @param DataObjectFactory $dataObjectFactory
     * @param ModeApiFactory $modeApiFactory
     */
    public function __construct(
        CountryCollection $countryCollection,
        RegionCollection $regionCollection,
        JsonHelper $jsonHelper,
        PostCodeFactory $postCodeFactory,
        ScopeConfigInterface $_scopeConfig,
        SourceMode $sourceMode,
        DataObjectFactory $dataObjectFactory,
        ModeApiFactory $modeApiFactory
    ) {
        $this->_countryCollection = $countryCollection;
        $this->_regionCollection  = $regionCollection;
        $this->jsonHelper         = $jsonHelper;
        $this->_scopeConfig       = $_scopeConfig;
        $this->_postCodeFactory   = $postCodeFactory;
        $this->_sourceMode        = $sourceMode;
        $this->_apiFactory        = $modeApiFactory;
        $this->_dataObjectFactory = $dataObjectFactory;
    }

    /**
     * @param $postCode
     * @return mixed
     */
    protected function _callApi($postCode)
    {
        $apiMode = $this->_scopeConfig->getValue(static::CONFIG_API_MODE);
        if (!$apiMode) {
            $apiMode = [];
            foreach($this->_sourceMode->toOptionArray() as $item) {
                array_push($apiMode, $item['value']);
            }
        }
        else {
            $apiMode = explode(",", $apiMode);
        }

        foreach ($apiMode as $className) {
            try {
                $apiClass = $this->_apiFactory->get($className);
                $return = $apiClass->getAddress($postCode);
            }
            catch (\Exception $exception) {
                continue;
            }

            if (!$return->getCompleted()) {
                continue;
            }

            if ($return) {
                return $return;
            }
        }

        $dataObject = $this->_dataObjectFactory->create();
        $dataObject->addData([
            'city'           => '',
            'country'        => '',
            'country_id'     => '',
            'region_id'      => '',
            'region'         => '',
            'completed'      => false,
            'display_fields' => false
        ]);

        return $dataObject;
    }

    /**
     * Get address using Google API by postcode
     *
     * @param string $postcode
     * @return array|bool
     */
    public function getAddressByPostcode($postCode)
    {
        if ($return = $this->getAddressByPostcodeCached($postCode)) {
            return $return;
        }

        $result = $this->_callApi($postCode);
        if ($result->getCompleted()) {
            $this->saveAddressByPostcodeCached($postCode, $result);
        }

        return $result->getData();
    }
    
    /**
     * @param string $postcode
     * @return array
     */
    protected function getAddressByPostcodeCached($postcode)
    {
        if (!$this->_scopeConfig->getValue(static::CONFIG_ENABLED_ZIP_CACHING)) {
            return null;
        }
        
        $postCodeModel = $this->_postCodeFactory->create();
        $postCodeModel->load($postcode, 'zip_code');
        if (!$postCodeModel->getEncodedData()) {
            return null;
        }

        $result = $this->jsonHelper->jsonDecode($postCodeModel->getEncodedData());
        $result['display_fields'] = isset($result['display_fields']) ? $result['display_fields'] : false;

        return $result;
    }
    
    /**
     * @param string $postcode
     * @param array $result
     */
    protected function saveAddressByPostcodeCached($postcode, $result)
    {
        if (!$this->_scopeConfig->getValue(static::CONFIG_ENABLED_ZIP_CACHING)) {
            return null;
        }
        
        $resultEncoded = $this->jsonHelper->jsonEncode($result->getData());
        
        $postCodeModel = $this->_postCodeFactory->create();
        $postCodeModel->setZipCode($postcode);
        $postCodeModel->setEncodedData($resultEncoded);
        $postCodeModel->save();
    }

    /**
     * @param  string $code
     * @return string
     */
    public function getCountryCode($code)
    {
        $country = $this->_countryCollection->addCountryCodeFilter($code)->getFirstItem();
        return $country->getCountryId() ?: '';
    }

    /**
     * @param  string $country_code
     * @param  string $code
     * @return string
     */
    public function getRegionId($country_code, $code)
    {
        $region = $this->_regionCollection->addCountryFilter($country_code)
        ->addRegionCodeFilter($code)
        ->getFirstItem();

        return $region->getRegionId() ?: '';
    }

    public function getConfigCountryAllow()
    {
        return $this->_scopeConfig->getValue(static::CONFIG_COUNTRY_ALLOW);
    }

    public function getConfigValue($config)
    {
        return $this->_scopeConfig->getValue($config);
    }
}