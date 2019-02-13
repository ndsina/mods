<?php

namespace GoMage\AdditionalProducts\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Area;
use Magento\Framework\App\State;
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
            \Magento\Catalog\Model\Category::ENTITY,
            'additional_products',
            [
                'group' => 'Additional products',
                'type' => 'text',
                'label' => 'Additional products',
                'input' => 'text',
                'global' => \Magento\Catalog\Model\ResourceModel\Eav\Attribute::SCOPE_GLOBAL,
                'sort_order' => 30,
                'visible' => true,
                'required' => false,
                'user_defined' => true,
                'default' => '',
            ]
        );

        $setup->endSetup();
    }
}
