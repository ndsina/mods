<?php
namespace GoMage\PostCode\Model\ResourceModel;

class PostCode extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('gomage_postcode', 'entity_id');
    }
}