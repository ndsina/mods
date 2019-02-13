<?php

namespace GoMage\BrandCategory\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Output\OutputInterface;

class BrandCategoriesSet extends Command
{
    const MESSAGE_INFO = 1;
    const MESSAGE_ERROR = 2;

    const LOG_PRODUCT_FILE = 'brand_categories.csv';

    /**
     * @var array
     */
    protected $_categoryNames = [];

    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $_categoryFactory;

    /**
     * @var \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory
     */
    protected $_productCollectionFactory;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $_productRepository;

    /**
     * @var \Magento\Catalog\Api\CategoryRepositoryInterface
     */
    protected $_categoryRepository;

    /**
     * @var string
     */
    protected $_logProductFile;

    /**
     * @var \Magento\ImportExport\Model\Export\Adapter\Csv
     */
    protected $_logCsvWrite;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $_output;

    /**
     * @var \Magento\ImportExport\Model\Export\Adapter\CsvFactory
     */
    protected $_csvWriterFactory;

    /**
     * @var \Magento\ImportExport\Model\Import\Source\CsvFactory
     */
    protected $_csvReaderFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    protected $_readFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     * @param \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory
     * @param \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Framework\App\Filesystem\DirectoryList $directoryList
     * @param \Magento\Framework\App\State $state
     * @param \Magento\ImportExport\Model\Export\Adapter\CsvFactory $csvWriterFactory
     * @param \Magento\ImportExport\Model\Import\Source\CsvFactory $csvReaderFactory
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param string|null $name
     */
    public function __construct(
        \Magento\Catalog\Model\CategoryFactory $categoryFactory,
        \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
        \Magento\Catalog\Api\CategoryRepositoryInterface $categoryRepository,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Magento\Framework\App\State $state,
        \Magento\ImportExport\Model\Export\Adapter\CsvFactory $csvWriterFactory,
        \Magento\ImportExport\Model\Import\Source\CsvFactory $csvReaderFactory,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        $name = null
    ) {
        $this->_categoryFactory = $categoryFactory;
        $this->_productCollectionFactory = $productCollectionFactory;
        $this->_categoryRepository = $categoryRepository;
        $this->_productRepository = $productRepository;
        $this->_storeManager = $storeManager;

        $this->_logProductFile = $directoryList->getPath('var') . '/' . static::LOG_PRODUCT_FILE; //@TODO

        $this->_csvWriterFactory = $csvWriterFactory;
        $this->_csvReaderFactory = $csvReaderFactory;

        $this->_filesystem = $filesystem;
        $this->_readFactory = $readFactory;

        parent::__construct($name);
    }
    
    /**
     * Configure
     */
    protected function configure()
    {
        $this->setName('catalog:brandcategories:set')
            ->setDescription('Set brand categories');

        $this->addArgument(
            'brand_category_id',
            InputArgument::REQUIRED,
            'Brand category id'
        );
        
        $this->addArgument(
            'file',
            InputArgument::OPTIONAL,
            'Name of import file'
        );
    }

    /**
     * @inheritdoc
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->_output = $output;
        
        $brandCategory = $input->getArgument('brand_category_id');
        $fileName = $input->getArgument('file');

        if ($fileName) {
            $this->_fixFileBrandCategories($brandCategory, $fileName);
        } else {
            $this->_fixSingleBrandCategories($brandCategory);
        }
    }

    /**
     * @param int $brandCategory
     * @throws \Exception
     */
    protected function _fixSingleBrandCategories($brandCategory)
    {
        $fileData = new \SplFileInfo($this->_logProductFile);

        $this->_logCsvWrite = $this->_csvWriterFactory->create([
            'destination' => $fileData->getFileName()
        ]);
        $this->_logCsvWrite->setHeaderCols(["product_sku","product_name","category_ids","category_info"]);

        $productCollection = $this->_productCollectionFactory->create();
        $productCollection->addAttributeToSelect(['brand_category_id', 'name'])
            ->addAttributeToFilter(
                'brand_category_id',
                array('null' => true),
                'left'
            )
            ->addAttributeToFilter('status', \Magento\Catalog\Model\Product\Attribute\Source\Status::STATUS_ENABLED);

        $this->_outputLn('Records (' . $productCollection->getSize() . ') ', static::MESSAGE_INFO);

        $counter = 0;
        /** @var \Magento\Catalog\Model\Product $productModel */
        foreach ($productCollection as $productModel) {
            $counter++;
            if ($counter % 100 == 0) {
                $this->_output('(' . $counter . ')');
            }

            $brandCategories = $this->_getBrandCategories($brandCategory, $productModel);
            
            if (count($brandCategories) != 1) {
                $this->_logProduct($productModel, $brandCategories);
                $this->_output('.');
                continue;
            }

            list($brandCategoryModel) = $brandCategories;
            $productModel->addAttributeUpdate('brand_category_id', $brandCategoryModel->getId(), $this->_storeManager->getStore()->getId());
            $this->_output('+');
        }

        $this->_outputLn('', static::MESSAGE_INFO);
        $this->_outputLn('Done. The "' . $this->_logProductFile . '" csv file was generated.', static::MESSAGE_INFO);
    }
    
    /**
     * @param int $brandCategory
     * @param string $filePath
     */
    protected function _fixFileBrandCategories($brandCategory, $filePath)
    {
        $fileData = new \SplFileInfo($filePath);
        $directory = $this->_readFactory->create($fileData->getPath());

        /** @var \Magento\ImportExport\Model\Import\Source\Csv $readCsv */
        $readCsv = $this->_csvReaderFactory->create([
            'file' => $fileData->getFileName(),
            'directory' => $directory,
        ]);

        $line = 0;

        $readCsv->rewind();
        while ($readCsv->valid()) {
            $data = new \Magento\Framework\DataObject($readCsv->current());
            $readCsv->next();
            $line++;

            if ($line % 100 == 0) {
                $this->_output('(' . $line . ')');
            }

            $productSku = $data->getData('product_sku');
            $productName = $data->getData('product_name');
            $categoryIds = $data->getData('category_ids');
            $categories = $data->getData('category_info');

            $categoryIds = explode(",", $categoryIds);

            foreach ($categoryIds as $key => &$item) {
                $item = trim($item);
                
                if (!$item) {
                    unset($categoryIds[$key]);
                }
            }

            if (count($categoryIds) != 1) {
                $this->_outputLn('');
                $this->_outputLn('Line ' . $line . ', product ' . $productSku . ' : Not selected or not single brand category.', static::MESSAGE_ERROR);
                continue;
            }

            try {
                $categoryModel = $this->_categoryRepository->get(
                    array_shift($categoryIds)
                );
            } catch (\Exception $e) {
                $this->_outputLn('', static::MESSAGE_ERROR);
                $this->_outputLn('Line ' . $line . ', product ' . $productSku . ' : ' . $e->getMessage(), static::MESSAGE_ERROR);
                continue;
            }

            try {
                /** @var \Magento\Catalog\Model\Product $productModel */
                $productModel = $this->_productRepository->get($productSku);
                $productModel->addAttributeUpdate('brand_category_id', $categoryModel->getId(), $this->_storeManager->getStore()->getId());
            } catch (\Exception $e) {
                $this->_outputLn('', static::MESSAGE_ERROR);
                $this->_outputLn('Line ' . $line . ', product ' . $productSku . ' : ' . $e->getMessage(), static::MESSAGE_ERROR);
                continue;
            }

            $this->_output('+');
        }
        
        $this->_outputLn('', static::MESSAGE_INFO);
        $this->_outputLn('Done.', static::MESSAGE_INFO);
    }
    
    /**
     * @param integer $brandCategory
     * @param \Magento\Catalog\Model\Product $productModel
     * @return array
     */
    protected function _getBrandCategories($brandCategory, $productModel)
    {
        $categoryCollection = $productModel->getCategoryCollection();
        $return = [];
        foreach ($categoryCollection as $categoryModel) {
            $pathArr = explode("/", $categoryModel->getPath());
            
            if (in_array($brandCategory, $pathArr)) {
                array_push($return, $categoryModel);
            }
        }
        
        return $return;
    }
    
    /**
     * @param integer $id
     * @return string
     */
    protected function _getCategoryName($id)
    {
        if (isset($this->_categoryNames[$id])) {
            return $this->_categoryNames[$id];
        }
        
        try {
            $this->_categoryNames[$id] = $this->_categoryRepository->get($id)->getName();
        } catch (\Exception $e) {
            $this->_outputLn('');
            $this->_outputLn('Error within category id ' . $id . ' to name tranformation: ' . $e->getMessage(), static::MESSAGE_ERROR);
            return null;
        }
        
        return $this->_categoryNames[$id];
    }
    
    /**
     * @param string $path
     * @return string
     */
    protected function _trasformPathToNames($path)
    {
        $path = explode("/", $path);
        
        foreach ($path as &$item) {
            $item = $this->_getCategoryName($item);
        }
        
        return implode(" > ", $path);
    }

    /**
     * @param \Magento\Catalog\Model\Product $productModel
     * @param array $brandCategories
     * @throws \Exception
     */
    protected function _logProduct($productModel, $brandCategories)
    {
        $brandCategoriesString = '';
        $brandCategoriesArr = [];
        
        foreach ($brandCategories as $brandCategoryModel) {
            if ($brandCategoriesString) {
                $brandCategoriesString = $brandCategoriesString . ' | ';
            }
            
            $path = $this->_trasformPathToNames($brandCategoryModel->getPath());
            
            array_push($brandCategoriesArr, $brandCategoryModel->getId());
            
            $brandCategoriesString = $brandCategoriesString .
                    $brandCategoryModel->getId() . ' (' . $path . ')';
        }
        
        $data = [
            'product_sku'   => $productModel->getSku(),
            'product_name'  => $productModel->getName(),
            'category_ids'  => implode(", ", $brandCategoriesArr),
            'category_info' => $brandCategoriesString
        ];
        
        $this->_logCsvWrite->writeRow($data);
    }
    
    /**
     * @param string $message
     * @param integer $messageType
     */
    protected function _outputLn($message = '', $messageType = null)
    {
        $this->_output($message, $messageType, true);
    }
    
    /**
     * @param string $message
     * @param integer $messageType
     * @param boolean $isLine
     * @return void
     */
    protected function _output($message = '', $messageType = null, $isLine = false)
    {
        if ($this->_output->getVerbosity() <= 1 && $messageType != static::MESSAGE_ERROR) {
            return;
        }
        
        switch ($messageType) {
            case (static::MESSAGE_ERROR):
                $message = "<error>" . $message . "</error>";
                break;
            case (static::MESSAGE_INFO):
                $message = "<info>" . $message . "</info>";
                break;
        }
        
        if ($isLine) {
            $this->_output->writeln($message);
        } else {
            $this->_output->write($message);
        }
        return;
    }
}
