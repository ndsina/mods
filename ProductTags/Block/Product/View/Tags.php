<?php

namespace GoMage\ProductTags\Block\Product\View;

use \Magento\Search\Model\QueryFactory;

class Tags extends \Magento\Catalog\Block\Product\View\AbstractView
{
    /**
     * Config path to 'Use meta keywords' catalog settings
     */
    const XML_PATH_USE_META_KEYWORDS = 'catalog/search/use_meta_keywords';

    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'GoMage_ProductTags::product/view/product_tags.phtml';

    /**
     * Product tags
     *
     * @var array
     */
    protected $_productTags = [];

    /**
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchHelper;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param \Magento\Framework\Stdlib\ArrayUtils $arrayUtils
     * @param \Magento\Search\Helper\Data $_searchHelper
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \Magento\Framework\Stdlib\ArrayUtils $arrayUtils,
        \Magento\Search\Helper\Data $_searchHelper,
        array $data = []
    ) {
        $this->_searchHelper = $_searchHelper;
        parent::__construct(
            $context,
            $arrayUtils,
            $data
        );
    }

    /**
     * @return array
     */
    public function getProductTags()
    {
        if (!$this->_productTags) {
            $useMeta = $this->_scopeConfig->isSetFlag(self::XML_PATH_USE_META_KEYWORDS);
            $product = $this->getProduct();
            $productTags = $product->getData('product_tags');
            if (!$productTags) {
                $productTags = $product->getData('meta_keyword');
                if (!$useMeta) {
                    return [];
                }
            }

            $productTags = explode(',', $productTags);
            array_walk(
                $productTags,
                function (&$a) {
                    $a = trim($a);
                }
            );
            $this->_productTags = $productTags;
        }
        return $this->_productTags;
    }

    /**
     * Retrieve search result page url and set "secure" param
     *
     * @param   string $query
     * @return  string
     */
    public function getSearchResultUrl($query = null)
    {
        return $this->_urlBuilder->getUrl(
            'catalogsearch/result',
            [
                '_query' => [QueryFactory::QUERY_VAR_NAME => $query],
                '_secure' => $this->_request->isSecure()
            ]
        );
    }
}
