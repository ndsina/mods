<?php

namespace GoMage\ProductDownloads\Setup;

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
        $downloads = $eavSetup->getAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads::CODE
        );
        if (!$downloads) {
            $eavSetup->addAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads::CODE,
                [
                    'group' => 'File Downloads',
                    'type' => 'text',
                    'label' => 'Downloads',
                    'input' => 'textarea',
                    'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                    'backend' => \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Backend\Downloads::class,
                    'sort_order' => 10,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => 0,
                    'used_in_product_listing' => true,
                    'is_used_in_grid' => false,
                    'is_filterable_in_grid' => false
                ]
            );
        } else {
            $eavSetup->updateAttribute(
                \Magento\Catalog\Model\Product::ENTITY,
                \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Downloads::CODE,
                'backend_model',
                \GoMage\ProductDownloads\Model\Catalog\Product\Attribute\Backend\Downloads::class
            );
        }

        $setup->endSetup();
    }
}
