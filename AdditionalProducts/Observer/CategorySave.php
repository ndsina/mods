<?php

namespace GoMage\AdditionalProducts\Observer;

use Magento\Framework\Event\ObserverInterface;

class CategorySave implements ObserverInterface
{
    /**
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $category   = $observer->getEvent()->getCategory();
        $request = $observer->getEvent()->getRequest();
        $data = $request->getPostValue();
        if (isset($data['category_products_grid'])
            && is_string($data['category_products_grid'])
        ) {
            $category->setAdditionalProducts($data['category_products_grid']);
        }
    }
}
