<?php

namespace GoMage\ProductTags\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
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
     */
    public function __construct(
        State $appState,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->appState = $appState;
        $this->eavSetupFactory = $eavSetupFactory;
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
            Product::ENTITY,
            'product_tags',
            [
                'group' => 'Tags',
                'type' => 'text',
                'label' => 'Product Tags',
                'input' => 'textarea',
                'wysiwyg_enabled' => false,
                'is_html_allowed_on_front' => false,
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'sort_order' => 10,
                'visible' => true,
                'used_in_product_listing' => false,
                'required' => false,
                'user_defined' => true,
                'default' => '',
            ]
        );

        $setup->endSetup();
    }
}
