<?php
namespace GoMage\PostCode\Model;

class PostCode extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init('GoMage\PostCode\Model\ResourceModel\PostCode');
    }
}