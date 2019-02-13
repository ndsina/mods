<?php

namespace GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Backend;

class Downloads extends \Magento\Eav\Model\Entity\Attribute\Backend\AbstractBackend
{
    /**
     * @var \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads
     */
    protected $_downloads;

    /**
     * @param \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads $downloads
     */
    public function __construct(
        \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads $downloads
    ) {
        $this->_downloads = $downloads;
    }

    /**
     * Before save method
     *
     * @param \Magento\Framework\DataObject $object
     * @return $this
     */
    public function beforeSave($object)
    {
        $attrCode = $this->getAttribute()->getAttributeCode();
        $data = $object->getData($attrCode);

//        $downloads = new \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads();

        if (is_array($data)) {
            $data = $this->prepareData($data);
            $this->_downloads->load($data);
        }

        $object->setData($attrCode, $this->_downloads->serialize());

        return $this;
    }

    /**
     * @param array $data
     * @return array
     */
    protected function prepareData($data)
    {
        $result = [];
        foreach ($data as $key => $value) {
            if (preg_match('/[0-9]/', $key)) {
                $new_key = preg_replace('/[0-9]+/', '', $key);
                if (!isset($result[$new_key])) {
                    $result[$new_key] = [];
                }
                $result[$new_key][] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

}
