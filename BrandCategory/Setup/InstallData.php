<?php

namespace GoMage\BrandCategory\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var State
     */
    protected $appState;

    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @param State $appState
     * @param EavSetupFactory $eavSetupFactory
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        State $appState,
        EavSetupFactory $eavSetupFactory,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->appState = $appState;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->appState->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'doInstall'], [$setup, $context]);
    }
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function doInstall(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        /** @var EavSetup $eavSetup */
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            $this->scopeConfig->getValue(\GoMage\BrandCategory\Helper\Data::XML_PATH_BRAND_ATTRIBUTE_CODE),
            [
                'group' => 'Product Details',
                'type' => 'int',
                'label' => 'Brand',
                'input' => 'select',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'sort_order' => 100,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => 0,
                'used_in_product_listing' => true,
                'is_used_in_grid' => true,
                'is_filterable_in_grid' => true
            ]
        );

        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            'brand_category_id',
            [
                'group' => 'Product Details',
                'type' => 'int',
                'label' => 'Brand Category',
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
                'input_renderer' => 'Magento\Catalog\Block\Adminhtml\Product\Helper\Form\Category',
                'required' => false,
                'sort_order' => 10,
                'visible' => true,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
            ]
        );

        $setup->endSetup();
    }
}
