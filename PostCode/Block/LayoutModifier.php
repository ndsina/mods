<?php
namespace GoMage\PostCode\Block;

class LayoutModifier implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;
    
    /**
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
        ) {
        $this->_scopeConfig = $scopeConfig;
    }
    
    /**
     * @param array $jsLayout
     * @return array
     */
    public function process($jsLayout)
    {
        if (!$this->_scopeConfig->getValue('gomage_postcode/gomage_postcode_group/disable_address_fields', \Magento\Store\Model\ScopeInterface::SCOPE_STORE)) {
            return $jsLayout;
        }

        if (isset($jsLayout['components']['checkout']['children']['steps']
            ['children']['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children']['region_id'])) {
            $regionIdField = &$jsLayout['components']['checkout']['children']['steps']
                ['children']['shipping-step']['children']['shippingAddress']['children']
                ['shipping-address-fieldset']['children']['region_id'];
            $regionIdField['disabled'] = true;
        }
        
        if ($jsLayout['components']['checkout']['children']['steps']
            ['children']['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children']['city']) {
            $cityField = &$jsLayout['components']['checkout']['children']['steps']
                ['children']['shipping-step']['children']['shippingAddress']['children']
                ['shipping-address-fieldset']['children']['city'];
            $cityField['disabled'] = true;
        }
        
        if ($jsLayout['components']['checkout']['children']['steps']
            ['children']['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children']['country_id']) {
            $countryIdField = &$jsLayout['components']['checkout']['children']['steps']
                ['children']['shipping-step']['children']['shippingAddress']['children']
                ['shipping-address-fieldset']['children']['country_id'];
            $countryIdField['disabled'] = true;
        }
        
        return $jsLayout;
    }
}
