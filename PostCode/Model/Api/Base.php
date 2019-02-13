<?php

namespace GoMage\PostCode\Model\Api;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Json\Helper\Data as JsonHelper;
use Magento\Framework\DataObjectFactory;
use Magento\Framework\HTTP\ZendClientFactory;
use GoMage\PostCode\Helper\Data as PostCodeHelper;


abstract class Base
{
    /**
     * @var LoggerInterface
     */
    protected $_logger;

    /**
     * @var JsonHelper
     */
    protected $_jsonHelper;

    /**
     * @var DataObjectFactory
     */
    protected $_dataObjectFactory;

    /**
     * @var ZendClientFactory
     */
    protected $_httpClientFactory;

    /**
     * Base constructor.
     * @param LoggerInterface $logger
     * @param JsonHelper $jsonHelper
     * @param DataObjectFactory $dataObjectFactory
     * @param ZendClientFactory $httpClientFactory
     * @param PostCodeHelper $postCodeHelper
     */
    public function __construct(
        LoggerInterface $logger,
        JsonHelper $jsonHelper,
        DataObjectFactory $dataObjectFactory,
        ZendClientFactory $httpClientFactory,
        PostCodeHelper $postCodeHelper
    ) {
        $this->_logger = $logger;
        $this->_jsonHelper = $jsonHelper;
        $this->_dataObjectFactory = $dataObjectFactory;
        $this->_httpClientFactory = $httpClientFactory;
        $this->_postCodeHelper = $postCodeHelper;
    }

    /**
     * @param $postCode
     * @return mixed
     * @throws \Zend_Http_Client_Exception
     */
    abstract public function getAddress($postCode);
}