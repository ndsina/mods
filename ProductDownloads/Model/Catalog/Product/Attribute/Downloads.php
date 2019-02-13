<?php

namespace GoMage\ProductDownloads\Model\Catalog\Product\Attribute;

class Downloads extends \ArrayObject
{
    const CODE = 'downloads';

    /**
     * @var array
     */
    protected $_brochure = ['', '', ''];

    /**
     * @var array
     */
    protected $_parts = ['', '', '', '', ''];

    /**
     * @var array
     */
    protected $_serviceManual = ['', '', '', '', ''];

    /**
     * @var string
     */
    protected $_specification = '';

    /**
     * @var string
     */
    protected $_ownerManual = '';

    /**
     * @var array
     */
    protected $_video = ['', '', '', '', '', '', '', '', '', ''];

    /**
     * @var array
     */
    protected $_cad = ['', '', '', '', ''];

    /**
     * @var array
     */
    protected $_diagram = ['', '', ''];

    /**
     * @var array
     */
    protected $_bulletin = ['', '', ''];

    /**
     * {@inheritdoc}
     */
    public function __construct($input = array(), $flags = 0, $iterator_class = "ArrayIterator")
    {
        parent::__construct($input, $flags, $iterator_class);
    }

    /**
     * @param bool|false $filter
     * @return array
     */
    public function getBrochure($filter = false)
    {
        // Fix after change string to array
        if (is_string($this->_brochure)) {
            $this->_brochure = [$this->_brochure, '', ''];
        }

        if ($filter) {
            return array_filter($this->_brochure);
        }
        return $this->_brochure;
    }

    /**
     * @param bool|true $filter
     * @return array
     */
    public function getParts($filter = false)
    {
        if ($filter) {
            return array_filter($this->_parts);
        }
        return $this->_parts;
    }

    /**
     * @param bool|true $filter
     * @return array
     */
    public function getDiagram($filter = false)
    {
        if ($filter) {
            return array_filter($this->_diagram);
        }
        return $this->_diagram;
    }

    /**
     * @param bool|true $filter
     * @return array
     */
    public function getBulletin($filter = false)
    {
        if ($filter) {
            return array_filter($this->_bulletin);
        }
        return $this->_bulletin;
    }

    /**
     * @param bool|false $filter
     * @return array
     */
    public function getServiceManual($filter = false)
    {
        if ($filter) {
            return array_filter($this->_serviceManual);
        }
        return $this->_serviceManual;
    }

    /**
     * @return string
     */
    public function getSpecification()
    {
        return $this->_specification;
    }

    /**
     * @return string
     */
    public function getOwnerManual()
    {
        return $this->_ownerManual;
    }

    /**
     * @param bool|false $filter
     * @return array
     */
    public function getVideo($filter = false)
    {
        if ($filter) {
            return array_filter($this->_video);
        }
        return $this->_video;
    }

    /**
     * @param bool|false $filter
     * @return array
     */
    public function getCad($filter = false)
    {
        if ($filter) {
            return array_filter($this->_cad);
        }
        return $this->_cad;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function load(array $data)
    {

        if (isset($data['brochure'])) {
            $this->_brochure = (array)$data['brochure'];
        }

        if (isset($data['parts'])) {
            $this->_parts = (array)$data['parts'];
        }

        if (isset($data['service_manual'])) {
            $this->_serviceManual = (array)$data['service_manual'];
        }

        if (isset($data['specification'])) {
            $this->_specification = $data['specification'];
        }

        if (isset($data['owner_manual'])) {
            $this->_ownerManual = $data['owner_manual'];
        }

        if (isset($data['video'])) {
            $this->_video = (array)$data['video'];
        }

        if (isset($data['cad'])) {
            $this->_cad = (array)$data['cad'];
        }

        if (isset($data['diagram'])) {
            $this->_diagram = (array)$data['diagram'];
        }

        if (isset($data['bulletin'])) {
            $this->_bulletin = (array)$data['bulletin'];
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isEmpty()
    {
        return empty($this->getBrochure(true)) &&
            empty($this->getParts(true)) &&
            empty($this->getServiceManual(true)) &&
            !$this->getSpecification() &&
            empty($this->getVideo(true)) &&
            empty($this->getCad(true)) &&
            !$this->getOwnerManual() &&
            empty($this->getDiagram(true)) &&
            empty($this->getBulletin(true));
    }

}