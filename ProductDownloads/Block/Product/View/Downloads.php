<?php

namespace GoMage\ProductDownloads\Block\Product\View;

use Magento\Framework\App\Filesystem\DirectoryList;

class Downloads extends \Magento\Catalog\Block\Product\AbstractProduct
{
    /**
     * Show all downloads
     * @var bool
     */
    protected $_showAll = false;
    /**
     * @var \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads
     */
    protected $_downloads;
    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadInterface
     */
    protected $_directory;

    /**
     * @param \Magento\Catalog\Block\Product\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Block\Product\Context $context,
        \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads $downloads,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->_directory = $this->_filesystem->getDirectoryRead(DirectoryList::ROOT);
        $this->_downloads = $downloads;

        if ($this->isActive()) {
            $this->setTemplate('GoMage_Pricing::product/view/downloads.phtml');
        }
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return $this->getSpecs() || $this->getFeatures() || $this->getManual() || !$this->getDownloads()->isEmpty();
    }

    /**
     * @return string
     */
    public function getSpecs()
    {
        if ($this->getProduct()) {
            $path = $this->getProduct()->getData('btn_product_specs');
            if ($this->_directory->isFile($path)) {
                return $path;
            }
        }
        return '';
    }

    /**
     * @return string
     */
    public function getFeatures()
    {
        if ($this->getProduct() && $this->getShowAll()) {
            $path = $this->getProduct()->getData('btn_exclusive_features');
            if ($this->_directory->isFile($path)) {
                return $path;
            }
        }
        return '';
    }

    /**
     * @return string
     */
    public function getManual()
    {
        if ($this->getProduct()) {
            $path = $this->getProduct()->getData('btn_owners_manual');
            if ($this->_directory->isFile($path)) {
                return $path;
            }
        }
        return '';
    }

    /**
     * @return \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads
     */
    public function getDownloads()
    {
        if ($this->getProduct()) {
            $data = $this->getProduct()->getData(\GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads::CODE);

            if ($data) {
                $this->_downloads->unserialize($data);
            }
        }
        return $this->_downloads;
    }

    /**
     * @return bool
     */
    public function getShowAll()
    {
        return $this->_showAll;
    }
}
