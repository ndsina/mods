<?php

namespace GoMage\PostCode\Plugin\Model;

use Magento\Quote\Api\Data\AddressInterface;

class ShippingMethodManagement
{
    /**
     * @var \GoMage\PostCode\Helper\Data
     */
    protected $postcodeHelper;

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var array
     */
    protected $_addressData = [];

    /**
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $_addressRepository;

    /**
     *
     * @param \GoMage\PostCode\Helper\Data $postcodeHelper
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Customer\Api\AddressRepositoryInterface $addressRepository,
        \GoMage\PostCode\Helper\Data $postcodeHelper
    ) {
        $this->_scopeConfig = $scopeConfig;
        $this->postcodeHelper = $postcodeHelper;
        $this->_addressRepository = $addressRepository;
    }

    /**
     * @param \Magento\Quote\Model\ShippingMethodManagement $subject
     * @param $cartId
     * @param AddressInterface $address
     * @return array
     */
    public function beforeEstimateByExtendedAddress(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        $cartId,
        AddressInterface $address
    ) {
        if ($postcode = trim($address->getPostcode())) {
            $addressData = $this->postcodeHelper->getAddressByPostcode($postcode);

            if (!$addressData['display_fields']) {
                if ($this->_isCallForQuote($addressData['region_id'], $postcode)) {
                    $addressData['call_for_quote'] = true;
                } else {
                    $addressData['call_for_quote'] = false;
                }

                $this->setData($addressData);
                $address->setCity($addressData['city']);
                $address->setRegionId($addressData['region_id']);
                $address->setRegion($addressData['region']);
                $address->setCountryId($addressData['country']);
            } else {
                $addressData = $address->getData();
                $addressData['display_fields'] = true;
                if ($this->_isCallForQuote($address->getRegionId(), $postcode)) {
                    $addressData['call_for_quote'] = true;
                } else {
                    $addressData['call_for_quote'] = false;
                }
                $this->setData($addressData);
            }
        }
        return [$cartId, $address];
    }

    /**
     * @param integer $regionId
     * @param string $postcode
     * @return boolean
     */
    protected function _isCallForQuote($regionId, $postcode)
    {
        if (!$this->_scopeConfig->getValue('gomage_postcode/gomage_postcode_group/enabled')) {
            return false;
        }

        $regionIds = $this->_scopeConfig->getValue('gomage_postcode/gomage_postcode_group/region_ids');
        $regionIds = explode(',', $regionIds);

        $appendCodes = $this->_scopeConfig->getValue('gomage_postcode/gomage_postcode_group/additional_zips');
        $appendCodes = explode(';', $appendCodes);
        foreach ($appendCodes as $key => &$item) {
            $item = trim($item);
            if (!$item) {
                unset($appendCodes[$key]);
            }
        }

        if (in_array($regionId, $regionIds) ||
            in_array($postcode, $appendCodes)
        ) {
            return true;
        }

        return false;
    }

    /**
     * @param \Magento\Quote\Model\ShippingMethodManagement $subject
     * @param array $result
     * @return array
     */
    public function afterEstimateByExtendedAddress(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        $result
    ) {
        if ($this->getData()) {
            $result[] = ['update_address' => $this->getData()];
        }
        return $result;
    }

    /**
     * @param \Magento\Quote\Model\ShippingMethodManagement $subject
     * @param \Closure $proceed
     * @param integer $quoteId
     * @param integer $addressId
     * @return array
     */
    public function aroundEstimateByAddressId(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        \Closure $proceed,
        $quoteId,
        $addressId
    ) {
        $address = $this->_addressRepository->getById($addressId);
        $postCode = $address->getPostcode();

        $result = $proceed($quoteId, $addressId);

        if ($this->_isCallForQuote($address->getRegionId(), $postCode)) {
            foreach ($result as &$item) {
                $item->setData('call_for_quote', true);
            }
        }

        return $result;
    }

    /**
     * @param \Magento\Quote\Model\ShippingMethodManagement $subject
     * @param \Closure $proceed
     * @param $quoteId
     * @param $addressId
     * @param $commercialStatus
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function aroundExtendedEstimateByAddressId(
        \Magento\Quote\Model\ShippingMethodManagement $subject,
        \Closure $proceed,
        $quoteId,
        $addressId,
        $commercialStatus
    ) {
        $address = $this->_addressRepository->getById($addressId);
        $postCode = $address->getPostcode();

        $result = $proceed($quoteId, $addressId, $commercialStatus);

        if ($this->_isCallForQuote($address->getRegionId(), $postCode)) {
            foreach ($result as &$item) {
                $item->setData('call_for_quote', true);
            }
        }

        return $result;
    }

    /**
     * @param array $data
     */
    protected function setData($data)
    {
        $this->_addressData = $data;
    }

    /**
     * @return array
     */
    protected function getData()
    {
        return $this->_addressData;
    }
}
