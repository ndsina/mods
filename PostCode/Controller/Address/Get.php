<?php

namespace GoMage\PostCode\Controller\Address;

use Magento\Framework\App\Action\Context;
use Magento\Framework\Json\Helper\Data as JsonHelper;

class Get extends \Magento\Framework\App\Action\Action
{
    /**
     * @var JsonHelper
     */
    protected $jsonHelper;

    /**
     * @var \GoMage\PostCode\Helper\Data
     */
    protected $postcodeHelper;

    /**
     * Get constructor.
     * @param Context $context
     * @param JsonHelper $jsonHelper
     * @param \GoMage\PostCode\Helper\Data $postcodeHelper
     */
    public function __construct(
        Context $context,
        JsonHelper $jsonHelper,
        \GoMage\PostCode\Helper\Data $postcodeHelper
    ) {
        parent::__construct($context);
        $this->jsonHelper = $jsonHelper;
        $this->postcodeHelper = $postcodeHelper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|void
     */
    public function execute()
    {
        $result = [
            'error' => true,
            'data'  => [
                'city'    => '',
                'country' => '',
                'region'  => '',
            ]
        ];
        $postcode = $this->getRequest()->getParam('postcode');
        if ($postcode) {
            if ($address = $this->postcodeHelper->getAddressByPostcode($postcode)) {
                $result['error'] = false;
                $result['data'] = $address;
            }
        }

        $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($result)
        );
    }
}