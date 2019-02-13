<?php

namespace GoMage\ProductDownloads\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Area;
use Magento\Eav\Setup\EavSetup;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\App\State;
use Magento\Catalog\Model\ResourceModel\Eav\Attribute as EavAttribute;
use Magento\Catalog\Model\Product;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    protected $eavSetupFactory;

    /**
     * @var State
     */
    protected $appState;

    /**
     * @param EavSetupFactory $eavSetupFactory
     * @param State $appState
     */
    public function __construct(
        EavSetupFactory $eavSetupFactory,
        State $appState
    )
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->appState = $appState;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->appState->emulateAreaCode(Area::AREA_ADMINHTML, [$this, 'doUpgrade'], [$setup, $context]);
    }


    public function doUpgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();
        if (version_compare($context->getVersion(), '0.2.0', '<')) {
            /** @var EavSetup $eavSetup */
            $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);
            $eavSetup->addAttribute(
                Product::ENTITY,
                'remove_pdf_previews',
                [
                    'group' => 'File Downloads',
                    'type' => 'int',
                    'label' => 'Remove PDF previews',
                    'input' => 'boolean',
                    'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',
                    'global' => EavAttribute::SCOPE_GLOBAL,
                    'sort_order' => 10,
                    'visible' => true,
                    'required' => false,
                    'user_defined' => true,
                    'default' => 0,
                    'used_in_product_listing' => true
                ]
            );
        }

        $setup->endSetup();
    }
}