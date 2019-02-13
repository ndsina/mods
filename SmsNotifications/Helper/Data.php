<?php

namespace GoMage\SmsNotifications\Helper;

use Magento\Config\Model\Config\Backend\Encrypted;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Enabled config path
     */
    const XML_PATH_SMS_ENABLED = 'gomage_sms/general/enabled';

    /**
     * CLX username config path
     */
    const XML_PATH_CLX_USERNAME = 'gomage_sms/general/username';

    /**
     * CLX token config path
     */
    const XML_PATH_CLX_TOKEN = 'gomage_sms/general/token';

    /**
     * CLX sender number config path
     */
    const XML_PATH_SENDER_NUMBER = 'gomage_sms/general/sender_number';

    /**
     * CLX sms body config path
     */
    const XML_PATH_SMS_BODY = 'gomage_sms/general/sms_body';

    /**
     * Block title config path
     */
    const XML_PATH_BLOCK_TITLE = 'gomage_sms/sms_block_settings/block_title';

    /**
     * Block text config path
     */
    const XML_PATH_BLOCK_TEXT = 'gomage_sms/sms_block_settings/block_text';

    /**
     * The default XMS endpoint.
     */
    const DEFAULT_ENDPOINT = "https://api.clxcommunications.com/xms/v1/";

    /**
     * Enabled country code config path
     */
    const XML_PATH_BLOCK_ENABLED_COUNTRY_CODE = 'gomage_sms/sms_block_settings/block_enabled_country_code';

    /**
     * Default country code config path
     */
    const XML_PATH_BLOCK_DEFAULT_COUNTRY_CODE = 'gomage_sms/sms_block_settings/block_default_country_code';

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var Encrypted
     */
    protected $encryption;
    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param Encrypted $encryption
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        Encrypted $encryption
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $registry;
        $this->_productRepository = $productRepository;
        $this->encryption = $encryption;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_SMS_ENABLED);
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_CLX_USERNAME);
    }

    /**
     * @return string
     */
    public function getToken()
    {
        $value = $this->encryption->processValue($this->scopeConfig->getValue(self::XML_PATH_CLX_TOKEN));
        return $value;
    }

    /**
     * @return string
     */
    public function getSenderNumber()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SENDER_NUMBER);
    }

    /**
     * @return string
     */
    public function getSmsBody()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_SMS_BODY);
    }

    /**
     * @return string
     */
    public function getBlockTitle()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_BLOCK_TITLE);
    }

    /**
     * @return string
     */
    public function getBlockText()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_BLOCK_TEXT);
    }

    /**
     * @return string
     */
    public function getEnabledCountryCode()
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_BLOCK_ENABLED_COUNTRY_CODE);
    }

    /**
     * @return string
     */
    public function getDefaultCountryCode()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_BLOCK_DEFAULT_COUNTRY_CODE);
    }

    /**
     * @return string
     */
    public function getSendUrl()
    {
        return self::DEFAULT_ENDPOINT . $this->getUsername();
    }

    /**
     * @param int $productId
     * @return \Magento\Catalog\Model\Product
     */
    public function getCurrentProduct($productId = null)
    {
        $product = $this->_coreRegistry->registry('current_product');
        if (!$product && $productId) {
            try {
                $product = $this->_productRepository->getById($productId);
            } catch (\Exception $e) {
                $product = null;
            }
        }
        return $product;
    }
}
