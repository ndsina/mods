<?php

namespace GoMage\ProductDownloads\Block\Product\View\Downloads;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Phrase;

/**
 * Customization product PDF
 *
 * @method Pdf setId(string $key)
 * @method int getId()
 * @method Pdf setProductRemovePreviews(bool $removePdfPreviews)
 * @method bool getProductRemovePreviews()
 * @method Pdf setTitle(string|Phrase $title)
 * @method string|Phrase getTitle()
 * @method Pdf setLinkTitle(string|Phrase $linkTitle)
 * @method string|Phrase getLinkTitle()
 * @method Pdf setComment(string|Phrase $comment)
 * @method string|Phrase getComment()
 */

class Pdf extends \Magento\Framework\View\Element\Template
{
    /**
     * @var array
     */
    protected $_allowedExtensions = ['pdf'];
    /**
     * Path to template file in theme.
     *
     * @var string
     */
    protected $_template = 'GoMage_ProductDownloads::product/view/downloads/pdf.phtml';

    /**
     * @var string
     */
    protected $docUrl;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\WriteInterface
     */
    protected $_directoryWrite;

    /**
     * @var \Magento\Framework\Image\AdapterFactory
     */
    protected $imageAdapter;

    /**
     * Pdf constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Image\AdapterFactory $imageAdapter
     * @param array $data
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Image\AdapterFactory $imageAdapter,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_filesystem = $context->getFilesystem();
        $this->_directoryWrite  = $this->_filesystem->getDirectoryWrite(DirectoryList::ROOT);
        $this->imageAdapter = $imageAdapter;
    }


    /**
     * @param  string $docUrl
     * @return $this
     */
    public function setDocUrl($docUrl)
    {
        $this->docUrl = $docUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getDocUrl()
    {
        return $this->docUrl;
    }

    /**
     * @return string
     */
    public function getFileExtension()
    {
        $fileExtension = pathinfo($this->getDocUrl(), PATHINFO_EXTENSION);
        return $fileExtension;
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function getImageUrl()
    {
        if ($this->_directoryWrite->isFile($this->getDocUrl()) && in_array($this->getFileExtension(), $this->_allowedExtensions)) {
            $imagePath = str_replace('.pdf', '.png', $this->getDocUrl());
            if ($this->getProductRemovePreviews() && $this->_directoryWrite->isFile($imagePath)) {
                $this->_directoryWrite->delete($imagePath);
            }
            if (!$this->_directoryWrite->isFile($imagePath)) {
                try {
                    /** @var \GoMage\ProductDownloads\Model\Image\Adapter\ImageMagick $adapter */
                    $adapter = $this->imageAdapter->create('IMAGEMAGICK');

                    /** @var \Imagick $image */
                    $image = $adapter->getImagickObject($this->getDocUrl());
                    $image->setFirstIterator();
                    $image->writeImage($this->_directoryWrite->getAbsolutePath($imagePath));
                } catch (\Exception | \ImagickException $e) {
                    $this->_logger->warning(__($e->getMessage() . ' ' . __FILE__));
                    return '';
                }
            }
            return $this->_storeManager->getStore()->getBaseUrl() . ltrim($imagePath, '/');
        }

        return '';
    }

}