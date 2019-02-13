<?php

namespace GoMage\SmsNotifications\Model;

use GoMage\SmsNotifications\Model\ResourceModel\Sms as SmsResource;
use Magento\Framework\Model\AbstractModel;

class Sms extends AbstractModel
{
    /**
     * Init model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(SmsResource::class);
    }
}
