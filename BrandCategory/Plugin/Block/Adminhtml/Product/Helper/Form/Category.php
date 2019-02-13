<?php

namespace GoMage\BrandCategory\Plugin\Block\Adminhtml\Product\Helper\Form;

use Magento\Framework\AuthorizationInterface;

class Category
{
    const BRAND_CATEGORY_ID = 'brand_category_id';

    /**
     * @var \Magento\Framework\Json\EncoderInterface
     */
    protected $_jsonEncoder;

    /**
     * @var AuthorizationInterface
     */
    protected $authorization;

    /**
     * Backend data
     *
     * @var \Magento\Backend\Helper\Data
     */
    protected $_backendData;

    /**
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param AuthorizationInterface $authorization
     * @param \Magento\Backend\Helper\Data $backendData
     */
    public function __construct(
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        AuthorizationInterface $authorization,
        \Magento\Backend\Helper\Data $backendData
    ) {
        $this->_jsonEncoder = $jsonEncoder;
        $this->authorization = $authorization;
        $this->_backendData = $backendData;
    }

    /**
     * Get the element as HTML
     *
     * @return string
     */
    public function aroundGetElementHtml(
        \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category $subject,
        \Closure $proceed
    )
    {
        $htmlId = $subject->getHtmlId();
        if ($htmlId != self::BRAND_CATEGORY_ID) {
            return $proceed();
        }

        $suggestPlaceholder = __('start typing to search category');
        $selectorOptions = $this->_jsonEncoder->encode($this->_getSelectorOptions($htmlId));

        $name = str_replace('[]', '', $subject->getName());
        $return = <<<HTML
    <input id="{$htmlId}" 
    placeholder="$suggestPlaceholder" 
    name="$name" {$subject->serialize($subject->getHtmlAttributes())} />
    <script>
        require(["jquery", "mage/mage"], function($){
            $('#{$htmlId}').mage('treeSuggest', {$selectorOptions});
        });
    </script>
HTML;

        $return .= $subject->getAfterElementHtml();

        return $return;
    }

    /**
     * @param \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category $subject
     * @param \Closure $proceed
     * @return mixed|string
     */
    public function aroundGetAfterElementHtml(
        \Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category $subject,
        \Closure $proceed
    )
    {
        $htmlId = $subject->getHtmlId();
        if ($htmlId != self::BRAND_CATEGORY_ID) {
            return $proceed();
        } elseif (!$this->isAllowed()) {
            return '';
        } else {
            return $subject->getData('after_element_html');
        }
    }

    /**
     * @param string $htmlId
     * @return array
     */
    protected function _getSelectorOptions($htmlId)
    {
        return [
            'source' => $this->_backendData->getUrl('catalog/category/suggestCategories'),
//            'valueField' => '#' . $htmlId,
            'className' => 'category-select',
            'multiselect' => false,
            'showAll' => true
        ];
    }

    /**
     * Whether permission is granted
     *
     * @return bool
     */
    protected function isAllowed()
    {
        return $this->authorization->isAllowed('Magento_Catalog::categories');
    }
}
