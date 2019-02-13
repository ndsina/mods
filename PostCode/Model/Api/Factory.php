<?php

namespace GoMage\PostCode\Model\Api;

class Factory
{
    const NAMESPACEFACTORY = 'GoMage\PostCode\Model\Api';

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * Factory constructor.
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     */
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->_objectManager = $objectManager;
    }

    /**
     * @param $className
     * @return mixed
     */
    public function get($className)
    {
        $className = static::NAMESPACEFACTORY . '\\' . $className;
        return $this->_objectManager->get($className);
    }
}
