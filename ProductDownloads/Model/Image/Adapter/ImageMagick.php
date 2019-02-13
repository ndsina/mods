<?php

namespace GoMage\ProductDownloads\Model\Image\Adapter;

class ImageMagick extends \Magento\Framework\Image\Adapter\ImageMagick
{
    /**
     * Get Imagick object
     *
     * @param mixed $files
     * @return \Imagick
     */
    public function getImagickObject($files = null)
    {
        return $this->_getImagickObject($files);
    }

    /**
     * Get ImagickDraw object
     *
     * @return \ImagickDraw
     */
    public function getImagickDrawObject()
    {
        return $this->_getImagickDrawObject();
    }

    /**
     * Get ImagickPixel object
     *
     * @param string|null $color
     * @return \ImagickPixel
     */
    public function getImagickPixelObject($color = null)
    {
        return $this->_getImagickPixelObject($color);
    }
}
