<?php

namespace GoMage\AdditionalProducts\Controller\Adminhtml\Category;

class Productsgrid extends \Magento\Catalog\Controller\Adminhtml\Category\Grid
{
    /**
     * Grid Action
     * Display list of products related to current category
     *
     * @return \Magento\Framework\Controller\Result\Raw|\Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $category = $this->_initCategory(true);
        if (!$category->getId()) {
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('catalog/*/', ['_current' => true, 'id' => null]);
        }
        /** @var \Magento\Framework\Controller\Result\Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'GoMage\AdditionalProducts\Block\Adminhtml\Category\Tab\Product',
                'gomage.category.product.grid'
            )->toHtml()
        );
    }
}
