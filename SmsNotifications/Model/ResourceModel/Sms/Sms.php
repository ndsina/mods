<?php

namespace GoMage\SmsNotifications\Model\ResourceModel\Sms;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use GoMage\SmsNotifications\Model\Sms as SmsModel;
use GoMage\SmsNotifications\Model\ResourceModel\Sms as SmsResource;

class Sms extends AbstractCollection
{
    protected function _construct()
    {
        $this->_init(SmsModel::class, SmsResource::class);
    }
}