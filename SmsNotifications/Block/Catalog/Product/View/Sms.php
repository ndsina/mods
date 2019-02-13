<?php

namespace GoMage\SmsNotifications\Block\Catalog\Product\View;

class Sms extends \Magento\Framework\View\Element\Template
{
    /**
     * @var \GoMage\SmsNotifications\Helper\Data
     */
    protected $smsHelper;

    /**
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \GoMage\SmsNotifications\Helper\Data $smsHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \GoMage\SmsNotifications\Helper\Data $smsHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->smsHelper = $smsHelper;
    }

    /**
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->smsHelper->getCurrentProduct();
    }

    /**
     * @return string
     */
    public function getFormActionUrl()
    {
        return $this->getUrl('gomage_sms/sms/send');
    }

    /**
     * @return string
     */
    public function getSmsTitle()
    {
        return $this->smsHelper->getBlockTitle();
    }

    /**
     * @return string
     */
    public function getSmsText()
    {
        return $this->smsHelper->getBlockText();
    }

    /**
     * @return string
     */
    public function getIsSmsCountryCodeEnabled()
    {
        return $this->smsHelper->getEnabledCountryCode();
    }

    /**
     * @return string
     */
    public function getSmsCountryCode()
    {
        return $this->smsHelper->getDefaultCountryCode();
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->smsHelper->isActive() && $this->getProduct()->getSendSms();
    }

}
