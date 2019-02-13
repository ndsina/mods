<?php

namespace GoMage\BrandCategory\Block\Product\View;

class Brand extends \Magento\Catalog\Block\Product\AbstractProduct
{

    /**
     * @var \GoMage\BrandCategory\Helper\Data
     */
    protected $_helper;

    /**
     * @var \GoMage\CategoryTemplates\Helper\Category\Image
     */
    protected $_imageCategoryHelper;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $_categoryRepository;

    /**
     * Compare constructor.
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \GoMage\BrandCategory\Helper\Data $helper
     * @param \GoMage\CategoryTemplates\Helper\Category\Image $imageCategoryHelper
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \GoMage\BrandCategory\Helper\Data $helper,
        \GoMage\CategoryTemplates\Helper\Category\Image $imageCategoryHelper,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_helper = $helper;
        $this->_imageCategoryHelper = $imageCategoryHelper;
        $this->_categoryRepository = $categoryRepository;
    }

    /**
     * @param int $width
     * @param int $height
     * @return \Magento\Framework\DataObject|null
     */
    public function getDataBrand($width = 180, $height = 180)
    {
        $product = $this->getProduct();
        if (!$product || !($categoryId = $product->getData('brand_category_id'))) {
            return null;
        }

        try {
            /** @var \Magento\Catalog\Model\Category $brandCategory */
            $brandCategory = $this->_categoryRepository->get($categoryId);
            $mainBrandCategory = $this->_helper->getMainProductBrandCategory($product, $brandCategory);
        } catch (\Exception $e) {
            return null;
        }

        $imageUrl = $this->_imageCategoryHelper
            ->init($mainBrandCategory, 'category_vendor_logo')
            ->constrainOnly(true)
            ->keepAspectRatio(true)
            ->keepFrame(false)
            ->resize($width, $height)
            ->getUrl();

        $data['brand'] = $product->getAttributeText($this->_helper->getBrandAttributeCode());
        $data['image'] = $imageUrl;
        $data['url'] = $mainBrandCategory->getUrl();
        $data['category'] = $brandCategory;

        return new \Magento\Framework\DataObject($data);
    }
}