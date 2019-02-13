<?php

namespace GoMage\SmsNotifications\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Sms extends AbstractDb
{
    /**
     * Init resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('gomage_sms', 'entity_id');
    }
}
