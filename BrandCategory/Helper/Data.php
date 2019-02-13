<?php

namespace GoMage\BrandCategory\Helper;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * Attributes excluded from comparison config path
     */
    const XML_PATH_BRAND_ATTRIBUTE_CODE = 'gomage_catalog/brand_category/brand_attribute_code';

    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var CategoryCollectionFactory
     */
    protected $_categoryCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        parent::__construct($context);
        $this->_scopeConfig = $context->getScopeConfig();
        $this->_categoryCollectionFactory = $categoryCollectionFactory;
    }

    /**
     * @return string
     */
    public function getBrandAttributeCode()
    {
        return $this->scopeConfig->getValue(self::XML_PATH_BRAND_ATTRIBUTE_CODE);
    }

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Category $brandCategoryDefault
     * @return mixed
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getMainProductBrandCategory($product, $brandCategoryDefault)
    {
        $brand = $product->getAttributeText($this->getBrandAttributeCode());
        if ($brand) {
            /** @var \Magento\Catalog\Model\ResourceModel\Category\Collection $categoryCollection */
            $categoryCollection = $this->_categoryCollectionFactory->create();
            $categoryCollection->addAttributeToSelect(['name', 'image'])
                ->addAttributeToFilter('name', ['like' => $brand . '%'])
                ->joinUrlRewrite()
                ->setOrder('level')
                ->setPage(1, 1);
            $brandCategory = $categoryCollection->getFirstItem();
        }

        return $brandCategory ?: $brandCategoryDefault;
    }
}