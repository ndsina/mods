<?php

namespace GoMage\AdditionalProducts\Block\Adminhtml\Category;

class AdditionalProducts extends \Magento\Catalog\Block\Adminhtml\Category\AssignProducts
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'GoMage_AdditionalProducts::catalog/category/edit/additional_products.phtml';

    /**
     * Retrieve instance of grid block
     *
     * @return \Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'GoMage\AdditionalProducts\Block\Adminhtml\Category\Tab\Product',
                'gomage.category.product.grid'
            );
        }
        return $this->blockGrid;
    }
    /**
     * @return string
     */
    public function getProductsJson()
    {
        $products = $this->getCategory()->getAdditionalProducts();
        if (!empty($products)) {
            return $products;
        }
        return '{}';
    }
}
