<?php

namespace GoMage\ProductDownloads\Block\Adminhtml\Catalog\Product\Edit;

use Magento\Backend\Block\Widget;
use Magento\Framework\Data\Form\Element\Renderer\RendererInterface;

class Downloads extends Widget implements RendererInterface
{
    /**
     * @var string
     */
    protected $_template = 'GoMage_ProductDownloads::product/edit/downloads.phtml';

    /**
     * Form element instance
     *
     * @var \Magento\Framework\Data\Form\Element\AbstractElement
     */
    protected $_element;

    /**
     * @var \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads
     */
    protected $_downloads;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads $downloads,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_downloads = $downloads;
    }

    /**
     * Render HTML
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->setElement($element);
        return $this->toHtml();
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return $this
     */
    public function setElement(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $this->_element = $element;
        return $this;
    }

    /**
     * Retrieve form element instance
     *
     * @return \Magento\Framework\Data\Form\Element\AbstractElement
     */
    public function getElement()
    {
        return $this->_element;
    }

    /**
     *
     * @return \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads
     */
    public function getValues()
    {
        /** @var \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads $data */
        $data = $this->getElement()->getValue();

        if ($data && is_string($data)) {
            $this->_downloads->unserialize($data);
        }

        return $this->_downloads;
    }

}
